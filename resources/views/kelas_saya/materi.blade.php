@php
    use Illuminate\Support\Str;
@endphp
@extends('layouts.app')
@section('title', 'Akses Kelas - ' . $course->title)
@section('content')
    <section class="py-8 bg-gradient-to-b from-primary-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header Course Info -->
            <!-- Header Course Info -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 p-6">
                <div class="md:flex md:justify-between md:items-start">

                    <!-- Info Course -->
                    <div class="md:w-2/3">
                        <div class="flex items-center mb-2">
                            <span class="bg-primary-100 text-white text-xs font-medium px-3 py-1 rounded-full mr-3">
                                {{ $course->nama_kategori ?? 'Umum' }}
                            </span>
                            <span class="text-sm text-gray-500">
                                Updated: {{ \Carbon\Carbon::parse($course->updated_at)->format('d M Y') }}
                            </span>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold text-primary-200 mb-3">{{ $course->title }}</h1>
                        <p class="text-gray-600 mb-4">{{ $course->description }}</p>

                        <div class="flex flex-col items-start space-y-2">
                            @php
                                $accessType = strtolower($course->access_type);
                                $badgeConfig = [
                                    'lifetime' => [
                                        'class' => 'bg-green-100 text-green-800',
                                        'icon' =>
                                            '<svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 111.414-1.414L8.414 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>',
                                        'label' => 'Lifetime Access',
                                    ],
                                    'subscription' => [
                                        'class' => 'bg-blue-100 text-blue-800',
                                        'icon' =>
                                            '<svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 4a1 1 0 10-2 0v4a1 1 0 00.553.894l3 1.5a1 1 0 10.894-1.788L11 9.382V6z"></path></svg>',
                                        'label' => 'Subscription',
                                    ],
                                ];
                                $badge = $badgeConfig[$accessType] ?? [
                                    'class' => 'bg-gray-100 text-gray-800',
                                    'icon' => '',
                                    'label' => ucfirst($course->access_type),
                                ];
                            @endphp
                            <span
                                class="flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $badge['class'] }}">
                                {!! $badge['icon'] !!} {{ $badge['label'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Tombol Download di sebelah kanan -->
                    <div class="mt-4 md:mt-0 md:ml-4 flex-shrink-0">
                        <div class="inline-block text-left">
                            <button type="button"
                                class="inline-flex justify-center bg-yellow-400 hover:bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                                id="dropdownButton" aria-expanded="true" aria-haspopup="true">
                                <i class="fa-solid fa-download mr-2"></i> Download Sertifikat
                                <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown menu -->
                            <div class="origin-top-right absolute right-0 mt-2 w-44 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
                                id="dropdownMenu">
                                <div class="py-1">
                                    <a href="{{ route('kelas.certificate.download', ['format' => 'pdf']) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Download sebagai PDF
                                    </a>
                                    <a href="{{ route('kelas.certificate.download', ['format' => 'image']) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Download sebagai Gambar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>



            @php
                $firstModuleId = $modules->isNotEmpty() ? $modules->first()->id : null;
            @endphp
            <div x-data="{ activeModule: {{ $firstModuleId ?? 'null' }} }" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">

                @forelse($modules as $module)
                    <div @click="activeModule = {{ $module->id }}; filterContentByModule({{ $module->id }});"
                        :class="activeModule === {{ $module->id }} ? 'bg-orange-100 border-orange-400' :
                            'bg-white border-gray-200'"
                        class="cursor-pointer rounded-lg shadow-md p-5 border transition">
                        <div class="flex items-center mb-3">
                            <div
                                class="w-12 h-12 bg-orange-200 text-white rounded-full flex items-center justify-center mr-4">
                                <i class="fa-solid fa-book"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">{{ $module->title }}</h3>
                                <p class="text-gray-500 text-sm line-clamp-2">
                                    {{ $module->description ?? 'Deskripsi singkat' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center text-gray-400 py-12">
                        <i class="fa-solid fa-folder-open text-4xl mb-4"></i>
                        <p>Belum ada modul untuk kelas ini.</p>
                    </div>
                @endforelse
            </div>

            <!-- Tab Navigation -->
            <!-- Ganti bagian Tab Navigation yang ada dengan kode ini -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px overflow-x-auto scrollbar-hide md:justify-center">
                        <button onclick="switchTab('materi', event)"
                            class="tab-button active flex-shrink-0 w-1/2 min-w-[120px] md:w-1/3 py-4 px-3 sm:px-6 text-center border-b-2 border-primary-100 text-primary-100 font-medium transition-colors duration-200">
                            <div class="flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-xs sm:text-sm">Materi</span>
                            </div>
                        </button>
                        @if ($videoContents->count() > 0)
                            <button onclick="switchTab('video', event)" id="video-tab"
                                class="tab-button flex-shrink-0 w-1/2 min-w-[120px] md:w-1/3 py-4 px-3 sm:px-6 text-center border-b-2 border-transparent text-gray-500 font-medium transition-colors duration-200">
                                <div class="flex items-center justify-center">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-xs sm:text-sm">Video</span>
                                </div>
                            </button>
                        @endif

                        <button onclick="switchTab('latihan', event)"
                            class="tab-button flex-shrink-0 w-1/2 min-w-[120px] md:w-1/3 py-4 px-3 sm:px-6 text-center border-b-2 border-transparent text-gray-500 font-medium transition-colors duration-200">
                            <div class="flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 01-1 1H8a1 1 0 01-1-1V9a1 1 0 011-1h4a1 1 0 011 1v2z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-xs sm:text-sm">Latihan</span>
                            </div>
                        </button>
                        <button onclick="switchTab('soal', event)"
                            class="tab-button flex-shrink-0 w-1/2 min-w-[120px] md:w-1/3 py-4 px-3 sm:px-6 text-center border-b-2 border-transparent text-gray-500 font-medium transition-colors duration-200">
                            <div class="flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-xs sm:text-sm">Try Out</span>
                            </div>
                        </button>
                    </nav>
                </div>
                <!-- Tab Content tetap sama seperti kode asli -->
                <div class="p-6">
                    <!-- Materi Tab -->
                    <div id="materi-content" class="tab-content">
                        <!-- Konten awal akan diisi oleh JavaScript -->
                    </div>

                    <!-- Video Tab -->
                    <div id="video-content" class="tab-content hidden">
                        <!-- Konten awal akan diisi oleh JavaScript -->
                    </div>

                    <!-- Latihan Tab -->
                    <div id="latihan-content" class="tab-content hidden">
                        <!-- Konten awal akan diisi oleh JavaScript -->
                    </div>

                    <!-- Soal Tab -->
                    <div id="soal-content" class="tab-content hidden">
                        <!-- Konten awal akan diisi oleh JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let allMateris = @json($materis);
        let allVideoContents = @json($videoContents);
        let allLatihan = @json($latihan);
        let allTryout = @json($tryout);
        let currentActiveTab = 'materi';

        // Simpan ID modul pertama
        const firstModuleId = @json($firstModuleId);

        function filterContentByModule(moduleId) {
            // Filter konten berdasarkan module_id
            const filteredMateris = moduleId ?
                allMateris.filter(materi => materi.module_id == moduleId) :
                allMateris;

            const filteredVideos = moduleId ?
                allVideoContents.filter(video => video.module_id == moduleId) :
                allVideoContents;

            const filteredLatihan = moduleId ?
                allLatihan.filter(latihan => latihan.module_id == moduleId) :
                allLatihan;

            const filteredTryout = moduleId ?
                allTryout.filter(tryout => tryout.module_id == moduleId) :
                allTryout;

            // Update semua tab content
            updateTabContent('materi', filteredMateris);
            updateTabContent('video', filteredVideos);
            updateTabContent('latihan', filteredLatihan);
            updateTabContent('soal', filteredTryout);
        }

        function updateTabContent(tabName, data) {
            const tabContent = document.getElementById(`${tabName}-content`);

            if (data.length === 0) {
                tabContent.innerHTML = `
                <div class="text-center py-12 text-gray-500">
                    <svg class="w-20 h-20 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-6 text-lg font-medium">Belum ada ${tabName} untuk modul ini.</p>
                </div>
            `;
                return;
            }

            let html = '';
            switch (tabName) {
                case 'materi':
                    html = generateMateriHTML(data);
                    break;
                case 'video':
                    html = generateVideoHTML(data);
                    break;
                case 'latihan':
                    html = generateLatihanHTML(data);
                    break;
                case 'soal':
                    html = generateTryoutHTML(data);
                    break;
            }

            tabContent.innerHTML = html;
        }

        function generateMateriHTML(materis) {
            return `
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-primary-200 mb-2">Materi Pembelajaran</h2>
                <p class="text-gray-600">Pelajari semua materi kelas secara bertahap</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                ${materis.map(materi => `
                                                                                                                                                                                                                                                                                                        <div class="content-card bg-gradient-to-br from-blue-50 to-white rounded-lg border border-blue-200 hover:border-primary-100 hover:shadow-md transition-all duration-300 group">
                                                                                                                                                                                                                                                                                                            <div class="p-6">
                                                                                                                                                                                                                                                                                                                <div class="flex items-center mb-4">
                                                                                                                                                                                                                                                                                                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4 text-blue-600 text-lg">
                                                                                                                                                                                                                                                                                                                        <i class="fa-solid fa-graduation-cap"></i>
                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                    <div class="flex-1">
                                                                                                                                                                                                                                                                                                                        <h3 class="font-semibold text-primary-200 group-hover:text-primary-100 transition-colors">
                                                                                                                                                                                                                                                                                                                            ${materi.title}
                                                                                                                                                                                                                                                                                                                        </h3>
                                                                                                                                                                                                                                                                                                                        <p class="text-sm text-gray-500">
                                                                                                                                                                                                                                                                                                                            ${materi.description || 'Deskripsi materi singkat'}
                                                                                                                                                                                                                                                                                                                        </p>
                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                <div class="flex items-center justify-between">
                                                                                                                                                                                                                                                                                                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                                                                                                                                                                                                                                                                                                                        Materi
                                                                                                                                                                                                                                                                                                                    </span>
                                                                                                                                                                                                                                                                                                                    <a href="/kelas/isi-materi/${materi.id}" target="_blank"
                                                                                                                                                                                                                                                                                                                        class="bg-primary-100 hover:bg-primary-200 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                                                                                                                                                                                                                                                                                                                        Mulai Belajar
                                                                                                                                                                                                                                                                                                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                                                                                                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                                                                                                                                                                                                                                                                                        </svg>
                                                                                                                                                                                                                                                                                                                    </a>
                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                    `).join('')}
            </div>
        `;
        }

        function generateVideoHTML(videos) {
            return `
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-primary-200 mb-2">Video Pembelajaran</h2>
                <p class="text-gray-600">Tonton video untuk memahami materi lebih mudah</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                ${videos.map(video => `
                                                                                                                                                                                                                                                                                                        <div class="bg-gradient-to-br from-purple-50 to-white rounded-lg border border-purple-200 hover:border-primary-100 hover:shadow-md transition-all duration-300">
                                                                                                                                                                                                                                                                                                            <div class="p-6">
                                                                                                                                                                                                                                                                                                                <div class="flex items-start justify-between mb-4">
                                                                                                                                                                                                                                                                                                                    <div class="flex-1">
                                                                                                                                                                                                                                                                                                                        <h3 class="text-lg font-semibold text-primary-200 mb-2">
                                                                                                                                                                                                                                                                                                                            ${video.title}
                                                                                                                                                                                                                                                                                                                        </h3>
                                                                                                                                                                                                                                                                                                                        <div class="flex items-center text-sm text-gray-500 mb-3">
                                                                                                                                                                                                                                                                                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                                                                                                                                                                                                                                                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                                                                                                                                                                                                                                                                                            </svg>
                                                                                                                                                                                                                                                                                                                            ${new Date(video.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-3 py-1 rounded-full">
                                                                                                                                                                                                                                                                                                                        Video
                                                                                                                                                                                                                                                                                                                    </span>
                                                                                                                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                                                                                                                ${video.video ? `
                                <div class="relative mb-4">
                                    <video class="w-full rounded-lg" controls controlsList="nodownload" oncontextmenu="return false;">
                                        <source src="{{ asset('storage/') }}/${video.video}" type="video/mp4">
                                        Browser Anda tidak mendukung pemutar video.
                                    </video>
                                </div>
                            ` : ''}

                                                                                                                                                                                                                                                                                                                ${video.file_pdf ? `
                                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm text-gray-600">Materi Pendukung</span>
                                    </div>
                                    <a href="{{ asset('storage/') }}/${video.file_pdf}" target="_blank"
                                        class="bg-primary-100 hover:bg-primary-200 text-white px-3 py-1 rounded text-sm transition-colors">
                                        Buka PDF
                                    </a>
                                </div>
                            ` : ''}
                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                    `).join('')}
            </div>
        `;
        }

        function generateLatihanHTML(latihanData) {
            return `
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-primary-200 mb-2">Latihan Soal</h2>
                <p class="text-gray-600">Uji pemahaman Anda dengan latihan-latihan berikut</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                ${latihanData.map(exercise => `
                                                                                                                                                                                                                                                                                                        <div class="content-card bg-gradient-to-br from-blue-50 to-white rounded-lg border border-blue-200 hover:border-primary-100 hover:shadow-md transition-all duration-300 group">
                                                                                                                                                                                                                                                                                                            <div class="p-6">
                                                                                                                                                                                                                                                                                                                <div class="flex items-center mb-4">
                                                                                                                                                                                                                                                                                                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                                                                                                                                                                                                                                                                                                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                                                                                                                                                                                                                                                                                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 01-1 1H8a1 1 0 01-1-1V9a1 1 0 011-1h4a1 1 0 011 1v2z" clip-rule="evenodd"></path>
                                                                                                                                                                                                                                                                                                                        </svg>
                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                    <div class="flex-1">
                                                                                                                                                                                                                                                                                                                        <h3 class="font-semibold text-primary-200 group-hover:text-primary-100 transition-colors">
                                                                                                                                                                                                                                                                                                                            ${exercise.title}
                                                                                                                                                                                                                                                                                                                        </h3>
                                                                                                                                                                                                                                                                                                                        <p class="text-sm text-gray-500">
                                                                                                                                                                                                                                                                                                                            ${exercise.quiz_type} • ${exercise.jumlah_soal} soal
                                                                                                                                                                                                                                                                                                                        </p>
                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                <p class="text-gray-600 text-sm mb-4">
                                                                                                                                                                                                                                                                                                                    Kumpulan soal latihan untuk menguji pemahaman materi yang telah dipelajari.
                                                                                                                                                                                                                                                                                                                </p>
                                                                                                                                                                                                                                                                                                                <div class="flex items-center justify-between">
                                                                                                                                                                                                                                                                                                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                                                                                                                                                                                                                                                                                                                        Latihan
                                                                                                                                                                                                                                                                                                                    </span>
                                                                                                                                                                                                                                                                                                                    <div class="flex space-x-2 mobile-button-wrapper">
                                                                                                                                                                                                                                                                                                                    <a href="/kelas/riwayat-nilai/${btoa(exercise.id)}"
                                                                                       class="border border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                                                                                        <i class="fa-solid fa-history mr-1"></i> Riwayat Nilai
                                                                                    </a>


                                                                                                                                                                                                                                                                                                                    <a href="javascript:void(0)"
                                                                                                                                                                                            class="btn-mulai bg-primary-100 hover:bg-primary-200 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center"
                                                                                                                                                                                            data-url="/kelas/latihan/${btoa(exercise.id)}"
                                                                                                                                                                                            data-title="${exercise.title}"
                                                                                                                                                                                            data-jumlah="${exercise.jumlah_soal}">
                                                                                                                                                                                            Mulai
                                                                                                                                                                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                                                                                                                                                            </svg>
                                                                                                                                                                                        </a>
                                                                                                                                                                                        </div>

                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                    `).join('')}
            </div>
        `;
        }

        function generateTryoutHTML(tryoutData) {
            return `
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-primary-200 mb-2">Tryout</h2>
                <p class="text-gray-600">Ikuti ujian untuk mendapatkan sertifikat</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                ${tryoutData.map(exercise => `
                                                                                                                                                                                                                                                                                                        <div class="content-card bg-gradient-to-br from-blue-50 to-white rounded-lg border border-blue-200 hover:border-primary-100 hover:shadow-md transition-all duration-300 group">
                                                                                                                                                                                                                                                                                                            <div class="p-6">
                                                                                                                                                                                                                                                                                                                <div class="flex items-center mb-4">
                                                                                                                                                                                                                                                                                                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                                                                                                                                                                                                                                                                                                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                                                                                                                                                                                                                                                                                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 01-1 1H8a1 1 0 01-1-1V9a1 1 0 011-1h4a1 1 0 011 1v2z" clip-rule="evenodd"></path>
                                                                                                                                                                                                                                                                                                                        </svg>
                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                    <div class="flex-1">
                                                                                                                                                                                                                                                                                                                        <h3 class="font-semibold text-primary-200 group-hover:text-primary-100 transition-colors">
                                                                                                                                                                                                                                                                                                                            ${exercise.title}
                                                                                                                                                                                                                                                                                                                        </h3>
                                                                                                                                                                                                                                                                                                                        <p class="text-sm text-gray-500">
                                                                                                                                                                                                                                                                                                                            ${exercise.quiz_type} • ${exercise.durasi ? exercise.durasi + ' menit' : '20 menit'} • ${exercise.jumlah_soal} soal
                                                                                                                                                                                                                                                                                                                        </p>
                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                <p class="text-gray-600 text-sm mb-4">
                                                                                                                                                                                                                                                                                                                    Kumpulan soal tryout untuk menguji pemahaman materi yang telah dipelajari.
                                                                                                                                                                                                                                                                                                                </p>
                                                                                                                                                                                                                                                                                                                <div class="flex items-center justify-between">
                                                                                                                                                                                                                                                                                                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                                                                                                                                                                                                                                                                                                                        Tryout
                                                                                                                                                                                                                                                                                                                    </span>
                                                                                                                                                                                                                                                                                                                    <div class="flex space-x-2 mobile-button-wrapper">
                                                                                                                                                                                                                                                                                                                    <a href="/kelas/riwayat-nilai/${btoa(exercise.id)}"
                                                                                       class="border border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                                                                                        <i class="fa-solid fa-history mr-1"></i> Riwayat Nilai
                                                                                    </a>
                                                                                                                                                                                                                                                                                                                    <a href="javascript:void(0)"
                                                                                                                                                                                        class="btn-mulai bg-primary-100 hover:bg-primary-200 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center"
                                                                                                                                                                                        data-url="/kelas/tryout/${btoa(exercise.id)}"
                                                                                                                                                                                        data-title="${exercise.title}"
                                                                                                                                                                                        data-jumlah="${exercise.jumlah_soal}">
                                                                                                                                                                                        Mulai
                                                                                                                                                                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                                                                                                                                                        </svg>
                                                                                                                                                                                    </a>
                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                    `).join('')}
            </div>
        `;
        }

        // Modifikasi fungsi switchTab
        function switchTab(tabName, event) {
            // Sembunyikan semua tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Reset semua tab button
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-primary-100', 'text-primary-100');
                button.classList.add('border-transparent', 'text-gray-500');
            });

            // Tampilkan tab content yang dipilih
            document.getElementById(tabName + '-content').classList.remove('hidden');

            // Tambahkan kelas aktif ke tab yang diklik
            event.currentTarget.classList.add('active', 'border-primary-100', 'text-primary-100');
            event.currentTarget.classList.remove('border-transparent', 'text-gray-500');

            currentActiveTab = tabName;
        }

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Aktifkan module pertama jika ada
            if (firstModuleId) {
                // Filter konten berdasarkan module pertama
                filterContentByModule(firstModuleId);

                // Set module pertama sebagai aktif di Alpine.js
                const alpineComponent = document.querySelector('[x-data]');
                alpineComponent.__x.$data.activeModule = firstModuleId;
            } else {
                // Jika tidak ada module, tampilkan semua konten
                filterContentByModule(null);
            }

            // Aktifkan tab materi secara default
            document.querySelector('[onclick="switchTab(\'materi\', event)"]').classList.add('active',
                'border-primary-100', 'text-primary-100');
            document.querySelector('[onclick="switchTab(\'materi\', event)"]').classList.remove(
                'border-transparent', 'text-gray-500');
        });

        // Event listener untuk video
        document.addEventListener('play', function(e) {
            var videos = document.querySelectorAll('video');
            for (var i = 0, len = videos.length; i < len; i++) {
                if (videos[i] != e.target) {
                    videos[i].pause();
                }
            }
        }, true);

        // Dropdown functionality
        const dropdownButton = document.getElementById('dropdownButton');
        const dropdownMenu = document.getElementById('dropdownMenu');

        if (dropdownButton && dropdownMenu) {
            dropdownButton.addEventListener('click', function(event) {
                event.preventDefault();
                dropdownMenu.classList.toggle('hidden');
            });

            window.addEventListener('click', function(e) {
                if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        }

        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-mulai')) {
                e.preventDefault();

                const btn = e.target.closest('.btn-mulai');
                const url = btn.getAttribute('data-url');
                const title = btn.getAttribute('data-title');
                const jumlah = btn.getAttribute('data-jumlah');

                Swal.fire({
                    title: `Mulai kerjakan ${title}`,
                    html: `
                <p class="text-gray-700">Kamu akan mulai mengerjakan soal <b>${title}</b>.</p>
                <p class="mt-2">Jumlah pertanyaan: <b>${jumlah}</b></p>
            `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Lanjutkan Latihan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            }
        });
    </script>
@endpush

@push('style')
    <style>
        .scrollbar-hide {
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* Internet Explorer 10+ */
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
            /* WebKit */
        }

        /* Responsif untuk mobile - 2 kolom per baris dengan horizontal scroll */
        @media (max-width: 768px) {

            /* Pastikan nav container dapat scroll horizontal */
            nav.flex {
                scroll-snap-type: x mandatory;
                padding-bottom: 2px;
                justify-content: flex-start !important;
                /* Override md:justify-center di mobile */
            }

            .tab-button {
                white-space: nowrap;
                scroll-snap-align: start;
                border-right: 1px solid #e5e7eb;
            }

            /* Hilangkan border kanan untuk tab terakhir */
            .tab-button:last-child {
                border-right: none;
            }

            /* Icon dan text spacing di mobile */
            .tab-button .flex {
                flex-direction: row;
                gap: 4px;
            }

            /* Visual indicator untuk scrollable content */
            nav.flex::after {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                width: 20px;
                height: 100%;
                background: linear-gradient(to left, white, transparent);
                pointer-events: none;
                z-index: 1;
            }
        }

        /* Desktop tetap seperti semula (md ke atas) */
        @media (min-width: 768px) {
            .tab-button {
                border-right: none;
                /* Tidak ada border di desktop */
            }

            nav.flex {
                overflow-x: visible;
                /* Tidak perlu scroll di desktop */
            }

            /* Hide gradient indicator di desktop */
            nav.flex::after {
                display: none;
            }
        }

        /* Smooth scrolling untuk nav tabs */
        nav.flex {
            scroll-behavior: smooth;
        }

        /* Hover effect tetap ada */
        .tab-button:hover {
            background-color: rgba(235, 99, 29, 0.05);
        }

        /* Active state styling */
        .tab-button.active {
            border-bottom: 2px solid #eb631d;
            color: #eb631d;
            font-weight: 600;
            background-color: rgba(235, 99, 29, 0.05);
        }

        /* Transisi yang smooth */
        .tab-button {
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }

        /* Styling untuk modal */
        #confirmationModal {
            transition: opacity 0.3s ease;
        }

        #confirmationModal:not(.hidden) {
            display: flex;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Video responsive */
        video {
            max-height: 400px;
            object-fit: contain;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(168, 192, 224, 0.15));
            animation: float 6s ease-in-out infinite;
        }

        .shape-1 {
            width: 60px;
            height: 60px;
            top: -30px;
            left: -30px;
            animation-delay: 0s;
            animation-duration: 8s;
        }

        .shape-2 {
            width: 40px;
            height: 40px;
            top: 50%;
            right: -20px;
            animation-delay: -2s;
            animation-duration: 6s;
        }

        .shape-3 {
            width: 30px;
            height: 30px;
            bottom: -15px;
            left: 30%;
            animation-delay: -4s;
            animation-duration: 7s;
        }

        .shape-4 {
            width: 45px;
            height: 45px;
            top: 20%;
            left: 70%;
            animation-delay: -1s;
            animation-duration: 5s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
                opacity: 0.3;
            }

            25% {
                transform: translateY(-10px) rotate(90deg);
                opacity: 0.6;
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
                opacity: 0.4;
            }

            75% {
                transform: translateY(-15px) rotate(270deg);
                opacity: 0.7;
            }
        }

        /* Animasi saat hover */
        .content-card:hover .shape {
            animation-duration: 3s;
        }

        .content-card:hover .floating-shapes {
            transform: scale(1.1);
            transition: transform 0.3s ease;
        }

        /* Partikel tambahan saat hover */
        .content-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
            z-index: 1;
        }

        .content-card:hover::before {
            left: 100%;
        }

        /* Efek shimmer pada card */
        .content-card {
            position: relative;
            overflow: hidden;
        }

        .content-card::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
            animation: shimmer 4s linear infinite;
            pointer-events: none;
        }

        @keyframes shimmer {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsif untuk mobile */
        @media (max-width: 768px) {
            .shape {
                animation-duration: 4s;
            }

            .shape-1 {
                width: 40px;
                height: 40px;
            }

            .shape-2 {
                width: 25px;
                height: 25px;
            }

            .shape-3 {
                width: 20px;
                height: 20px;
            }

            .shape-4 {
                width: 30px;
                height: 30px;
            }
        }


        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tab-button.active {
            border-bottom: 2px solid #eb631d;
            /* garis bawah */
            color: #eb631d;
            /* warna teks */
            font-weight: 600;
        }

        .tab-button {
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }



        /* Custom scrollbar */
        .content-card::-webkit-scrollbar {
            width: 6px;
        }

        .content-card::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .content-card::-webkit-scrollbar-thumb {
            background: #eb631d;
            border-radius: 3px;
        }

        .content-card::-webkit-scrollbar-thumb:hover {
            background: #d45615;
        }

        /* Smooth transitions */
        .tab-content {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        @media (max-width: 768px) {

            /* Container tombol di kartu latihan dan tryout */
            .content-card .flex.items-center.justify-between {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            /* Container untuk kedua tombol */
            .content-card .flex.space-x-2 {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            /* Tombol individual */
            .content-card a {
                justify-content: center;
                text-align: center;
                width: 100%;
            }

            /* Badge kategori */
            .content-card .text-xs.bg-yellow-100 {
                align-self: center;
                margin-bottom: 8px;
            }
        }

        @media (min-width: 769px) {
            .content-card .flex.items-center.justify-between {
                flex-direction: row;
                align-items: center;
            }

            .content-card .flex.space-x-2 {
                flex-direction: row;
                gap: 8px;
            }

            .content-card a {
                width: auto;
            }
        }
    </style>
@endpush
