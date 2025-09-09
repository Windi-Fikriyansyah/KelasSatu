@extends('layouts.app')
@section('title', $module->title)
@section('content')
    <div class="min-h-screen bg-gray-100 py-4 px-2 sm:px-4 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm mb-4 p-4">
                <div class="flex items-center gap-3">

                    <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-2">{{ $module->title }}</h1>
                </div>

                <!-- Controls -->
                <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                    <div class="flex items-center space-x-2">
                        <button id="prev-page"
                            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                            ← Prev
                        </button>
                        <span id="page-info" class="text-sm text-gray-600 whitespace-nowrap">
                            Page <span id="current-page">1</span> of <span id="total-pages">-</span>
                        </span>
                        <button id="next-page"
                            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                            Next →
                        </button>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button id="zoom-out"
                            class="px-2 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">-</button>
                        <span id="zoom-level" class="text-sm text-gray-600 min-w-[60px] text-center">100%</span>
                        <button id="zoom-in"
                            class="px-2 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">+</button>
                        <button id="fit-width" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-sm">
                            Fit Width
                        </button>
                    </div>

                    <!-- Mobile menu toggle -->
                    <button id="mobile-controls"
                        class="sm:hidden px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
                        Controls
                    </button>
                </div>

                <!-- Mobile controls panel -->
                <div id="mobile-panel" class="sm:hidden mt-3 p-3 bg-gray-50 rounded hidden">
                    <div class="space-y-2">
                        <div class="text-sm text-gray-600">Use pinch to zoom, swipe to navigate</div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Quick zoom:</span>
                            <div class="space-x-1">
                                <button class="zoom-preset px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs"
                                    data-scale="0.5">50%</button>
                                <button class="zoom-preset px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs"
                                    data-scale="1">100%</button>
                                <button class="zoom-preset px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs"
                                    data-scale="1.5">150%</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PDF Container -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div id="pdf-container" class="relative overflow-auto" style="max-height: calc(100vh - 200px);">
                    <div id="loading" class="flex justify-center items-center py-20">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                        <span class="ml-3 text-gray-600">Loading PDF...</span>
                    </div>
                    <canvas id="pdf-canvas" class="mx-auto block" style="display: none;"></canvas>

                    <!-- Error message -->
                    <div id="error-message" class="text-center py-20 text-red-600 hidden">
                        <p class="text-lg mb-2">Failed to load PDF</p>
                        <p class="text-sm text-gray-500">Please check if the file exists and try refreshing the page</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const url = "{{ asset('storage/' . $module->file_pdf) }}";
            pdfjsLib.GlobalWorkerOptions.workerSrc =
                "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js";

            // Elements
            const canvas = document.getElementById('pdf-canvas');
            const ctx = canvas.getContext('2d');
            const container = document.getElementById('pdf-container');
            const loading = document.getElementById('loading');
            const errorMessage = document.getElementById('error-message');
            const currentPageSpan = document.getElementById('current-page');
            const totalPagesSpan = document.getElementById('total-pages');
            const zoomLevelSpan = document.getElementById('zoom-level');

            // State
            let pdfDoc = null;
            let currentPage = 1;
            let currentScale = 1;
            let originalViewport = null;

            // Get container width for responsive scaling
            function getContainerWidth() {
                return container.clientWidth - 40; // Account for padding
            }

            // Calculate fit-to-width scale
            function getFitWidthScale() {
                if (!originalViewport) return 1;
                const containerWidth = getContainerWidth();
                return containerWidth / originalViewport.width;
            }

            // Render page
            function renderPage(pageNum, scale = null) {
                if (scale === null) scale = currentScale;

                pdfDoc.getPage(pageNum).then(page => {
                    if (!originalViewport) {
                        originalViewport = page.getViewport({
                            scale: 1
                        });
                    }

                    const viewport = page.getViewport({
                        scale: scale
                    });

                    // Set canvas size
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    // Update UI
                    currentPageSpan.textContent = pageNum;
                    zoomLevelSpan.textContent = Math.round(scale * 100) + '%';

                    // Render
                    const renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };

                    page.render(renderContext).promise.then(() => {
                        canvas.style.display = 'block';
                        loading.style.display = 'none';
                    });
                }).catch(error => {
                    console.error('Error rendering page:', error);
                    showError();
                });
            }

            // Show error
            function showError() {
                loading.style.display = 'none';
                canvas.style.display = 'none';
                errorMessage.classList.remove('hidden');
            }

            // Update navigation buttons
            function updateNavButtons() {
                document.getElementById('prev-page').disabled = currentPage <= 1;
                document.getElementById('next-page').disabled = currentPage >= pdfDoc.numPages;
            }

            // Initialize PDF
            pdfjsLib.getDocument(url).promise.then(pdf => {
                pdfDoc = pdf;
                totalPagesSpan.textContent = pdf.numPages;

                // Set initial scale to fit width on mobile, normal scale on desktop
                if (window.innerWidth < 768) {
                    currentScale = getFitWidthScale();
                } else {
                    currentScale = 1.2;
                }

                renderPage(currentPage, currentScale);
                updateNavButtons();

            }).catch(error => {
                console.error('Error loading PDF:', error);
                showError();
            });

            // Navigation controls
            document.getElementById('prev-page').addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderPage(currentPage);
                    updateNavButtons();
                }
            });

            document.getElementById('next-page').addEventListener('click', () => {
                if (currentPage < pdfDoc.numPages) {
                    currentPage++;
                    renderPage(currentPage);
                    updateNavButtons();
                }
            });

            // Zoom controls
            document.getElementById('zoom-in').addEventListener('click', () => {
                currentScale = Math.min(currentScale * 1.2, 5);
                renderPage(currentPage);
            });

            document.getElementById('zoom-out').addEventListener('click', () => {
                currentScale = Math.max(currentScale / 1.2, 0.2);
                renderPage(currentPage);
            });

            document.getElementById('fit-width').addEventListener('click', () => {
                currentScale = getFitWidthScale();
                renderPage(currentPage);
            });

            // Mobile controls
            document.getElementById('mobile-controls').addEventListener('click', () => {
                const panel = document.getElementById('mobile-panel');
                panel.classList.toggle('hidden');
            });

            // Zoom presets
            document.querySelectorAll('.zoom-preset').forEach(btn => {
                btn.addEventListener('click', () => {
                    currentScale = parseFloat(btn.dataset.scale);
                    renderPage(currentPage);
                });
            });

            // Touch/swipe support for mobile
            let touchStartX = 0;
            let touchStartY = 0;

            // canvas.addEventListener('touchstart', (e) => {
            //     touchStartX = e.touches[0].clientX;
            //     touchStartY = e.touches[0].clientY;
            // });

            // canvas.addEventListener('touchend', (e) => {
            //     if (!touchStartX || !touchStartY) return;

            //     const touchEndX = e.changedTouches[0].clientX;
            //     const touchEndY = e.changedTouches[0].clientY;

            //     const deltaX = touchStartX - touchEndX;
            //     const deltaY = Math.abs(touchStartY - touchEndY);

            //     // Only trigger if horizontal swipe is dominant
            //     if (Math.abs(deltaX) > deltaY && Math.abs(deltaX) > 50) {
            //         if (deltaX > 0 && currentPage < pdfDoc.numPages) {
            //             // Swipe left - next page
            //             currentPage++;
            //             renderPage(currentPage);
            //             updateNavButtons();
            //         } else if (deltaX < 0 && currentPage > 1) {
            //             // Swipe right - previous page
            //             currentPage--;
            //             renderPage(currentPage);
            //             updateNavButtons();
            //         }
            //     }

            //     touchStartX = 0;
            //     touchStartY = 0;
            // });

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft' && currentPage > 1) {
                    currentPage--;
                    renderPage(currentPage);
                    updateNavButtons();
                } else if (e.key === 'ArrowRight' && currentPage < pdfDoc.numPages) {
                    currentPage++;
                    renderPage(currentPage);
                    updateNavButtons();
                }
            });

            // Responsive handling
            window.addEventListener('resize', () => {
                // Recalculate scale if needed
                if (window.innerWidth < 768) {
                    const newScale = getFitWidthScale();
                    if (Math.abs(currentScale - newScale) > 0.1) {
                        currentScale = newScale;
                        renderPage(currentPage);
                    }
                }
            });
        });
    </script>
@endpush
