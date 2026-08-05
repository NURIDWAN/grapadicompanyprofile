{{--
    Alpine component powering the asset photo gallery and its zoomable lightbox.

    Registered on `alpine:init` so it is available before Alpine evaluates the
    `x-data="assetGallery(...)"` expression in the markup.

    Supported interactions:
      - Click main photo / zoom button  -> open lightbox
      - Mouse wheel                     -> zoom toward cursor
      - Double click                    -> toggle 2.5x zoom at cursor
      - Drag (when zoomed)              -> pan
      - Pinch (touch)                   -> zoom
      - Swipe (touch, when not zoomed)  -> previous / next photo
      - Arrow keys / Escape             -> navigate / close
--}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('assetGallery', (total = 1) => ({
            active: 0,
            total: total,
            touchStart: 0,

            // Lightbox + zoom state
            open: false,
            scale: 1,
            minScale: 1,
            maxScale: 6,
            tx: 0,
            ty: 0,
            dragging: false,
            lastX: 0,
            lastY: 0,
            pinchStart: 0,
            pinchScale: 1,

            get zoomed() {
                return this.scale > 1.01;
            },

            next() {
                this.active = (this.active + 1) % this.total;
                this.resetZoom();
            },

            previous() {
                this.active = (this.active - 1 + this.total) % this.total;
                this.resetZoom();
            },

            show(index) {
                this.active = index;
                this.resetZoom();
            },

            finishSwipe(event) {
                if (! event.changedTouches || event.changedTouches.length === 0) {
                    return;
                }

                const distance = event.changedTouches[0].clientX - this.touchStart;

                if (Math.abs(distance) > 45) {
                    distance < 0 ? this.next() : this.previous();
                }
            },

            openLightbox(index) {
                if (typeof index === 'number') {
                    this.active = index;
                }

                this.resetZoom();
                this.open = true;
                document.body.style.overflow = 'hidden';
            },

            closeLightbox() {
                this.open = false;
                this.resetZoom();
                document.body.style.overflow = '';
            },

            resetZoom() {
                this.scale = 1;
                this.tx = 0;
                this.ty = 0;
                this.dragging = false;
                this.pinchStart = 0;
            },

            clamp(value) {
                return Math.min(this.maxScale, Math.max(this.minScale, value));
            },

            stageRect() {
                return this.$refs.stage ? this.$refs.stage.getBoundingClientRect() : null;
            },

            // The visible size of the active photo. Because the image uses
            // object-contain, its element box matches the stage while the drawn
            // content is letterboxed inside it -- panning must respect the
            // content, otherwise the image can be dragged into empty space.
            contentSize() {
                const rect = this.stageRect();

                if (! rect) {
                    return null;
                }

                const img = this.$refs.stage.querySelector(`img[data-index="${this.active}"]`);

                if (! img || ! img.naturalWidth || ! img.naturalHeight) {
                    return { width: rect.width, height: rect.height };
                }

                const fit = Math.min(rect.width / img.naturalWidth, rect.height / img.naturalHeight);

                return {
                    width: img.naturalWidth * fit,
                    height: img.naturalHeight * fit,
                };
            },

            // Coordinates relative to the stage centre, which is also the
            // transform origin of the image.
            stagePoint(event) {
                const rect = this.stageRect();

                if (! rect) {
                    return { x: 0, y: 0 };
                }

                return {
                    x: event.clientX - rect.left - rect.width / 2,
                    y: event.clientY - rect.top - rect.height / 2,
                };
            },

            // Keep the point under the cursor anchored while scaling.
            zoomBy(factor, cx, cy) {
                const target = this.clamp(this.scale * factor);

                if (target === this.scale) {
                    return;
                }

                const ratio = target / this.scale;
                const x = cx || 0;
                const y = cy || 0;

                this.tx = x - (x - this.tx) * ratio;
                this.ty = y - (y - this.ty) * ratio;
                this.scale = target;

                if (! this.zoomed) {
                    this.tx = 0;
                    this.ty = 0;
                }

                this.constrain();
            },

            // Prevent the image being panned completely out of view.
            constrain() {
                const rect = this.stageRect();
                const content = this.contentSize();

                if (! rect || ! content) {
                    return;
                }

                const maxX = Math.max(0, (content.width * this.scale - rect.width) / 2);
                const maxY = Math.max(0, (content.height * this.scale - rect.height) / 2);

                this.tx = Math.min(maxX, Math.max(-maxX, this.tx));
                this.ty = Math.min(maxY, Math.max(-maxY, this.ty));
            },

            zoomIn() {
                this.zoomBy(1.4, 0, 0);
            },

            zoomOut() {
                this.zoomBy(1 / 1.4, 0, 0);
            },

            onWheel(event) {
                const point = this.stagePoint(event);

                this.zoomBy(event.deltaY < 0 ? 1.18 : 1 / 1.18, point.x, point.y);
            },

            toggleZoom(event) {
                if (this.zoomed) {
                    this.resetZoom();

                    return;
                }

                const point = this.stagePoint(event);

                this.zoomBy(2.5, point.x, point.y);
            },

            startDrag(event) {
                if (! this.zoomed) {
                    return;
                }

                this.dragging = true;
                this.lastX = event.clientX;
                this.lastY = event.clientY;
            },

            onDrag(event) {
                if (! this.dragging) {
                    return;
                }

                this.tx += event.clientX - this.lastX;
                this.ty += event.clientY - this.lastY;
                this.lastX = event.clientX;
                this.lastY = event.clientY;
                this.constrain();
            },

            endDrag() {
                this.dragging = false;
            },

            pinchDistance(touches) {
                return Math.hypot(
                    touches[0].clientX - touches[1].clientX,
                    touches[0].clientY - touches[1].clientY
                );
            },

            onTouchStart(event) {
                if (event.touches.length === 2) {
                    this.pinchStart = this.pinchDistance(event.touches);
                    this.pinchScale = this.scale;

                    return;
                }

                if (event.touches.length === 1) {
                    if (this.zoomed) {
                        this.startDrag(event.touches[0]);
                    } else {
                        this.touchStart = event.touches[0].clientX;
                    }
                }
            },

            onTouchMove(event) {
                if (event.touches.length === 2 && this.pinchStart) {
                    this.scale = this.clamp(this.pinchScale * (this.pinchDistance(event.touches) / this.pinchStart));

                    if (! this.zoomed) {
                        this.tx = 0;
                        this.ty = 0;
                    }

                    this.constrain();

                    return;
                }

                if (event.touches.length === 1 && this.dragging) {
                    this.onDrag(event.touches[0]);
                }
            },

            onTouchEnd(event) {
                const wasPinching = this.pinchStart > 0;
                const wasDragging = this.dragging;

                this.pinchStart = 0;
                this.endDrag();

                // Only treat the gesture as a swipe when the image is at rest.
                if (! wasPinching && ! wasDragging && ! this.zoomed && event.touches.length === 0) {
                    this.finishSwipe(event);
                }
            },
        }));
    });
</script>
