@extends('layouts.app')
@section('title', $module->title)
@section('content')
    <div class="min-h-screen bg-gray-100 py-4 px-2 sm:px-4 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->

            <div class="bg-white rounded-lg shadow-sm mb-4 p-4 flex items-center">
                <a href="javascript:history.back()" class="mr-4 text-gray-600 hover:text-primary-100 transition-colors">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-2">{{ $module->title }}</h1>
            </div>

            <!-- PDF Container -->
            <div class="bg-white rounded-lg shadow-sm overflow-auto p-4">
                <div id="pdf-container" class="space-y-6">
                    <div id="loading" class="flex justify-center items-center py-20">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                        <span class="ml-3 text-gray-600">Loading PDF...</span>
                    </div>
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

            const container = document.getElementById('pdf-container');
            const loading = document.getElementById('loading');
            const errorMessage = document.getElementById('error-message');

            function showError() {
                loading.style.display = 'none';
                errorMessage.classList.remove('hidden');
            }

            function renderPage(page, scale = 1.2) {
                const viewport = page.getViewport({
                    scale
                });
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                canvas.classList.add('mx-auto', 'block', 'rounded', 'shadow-sm');
                container.appendChild(canvas);

                return page.render({
                    canvasContext: ctx,
                    viewport
                }).promise;
            }

            pdfjsLib.getDocument(url).promise.then(pdf => {
                loading.style.display = 'none';
                const scale = window.innerWidth < 768 ? (container.clientWidth - 32) / pdf.getPage(1).then(
                    p => p.getViewport({
                        scale: 1
                    }).width) : 1.2;

                let renderPromises = [];
                for (let i = 1; i <= pdf.numPages; i++) {
                    renderPromises.push(pdf.getPage(i).then(page => renderPage(page, scale)));
                }

                Promise.all(renderPromises).catch(err => {
                    console.error(err);
                    showError();
                });
            }).catch(err => {
                console.error(err);
                showError();
            });
        });
    </script>
@endpush
