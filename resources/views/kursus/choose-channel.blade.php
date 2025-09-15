@extends('layouts.app')
@section('title', 'Pilih Channel Pembayaran')

@section('content')
    <section class="py-20 bg-primary-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="text-2xl font-bold mb-6">Pilih Metode Pembayaran</h2>

            <div class="grid md:grid-cols-2 gap-4">
                @foreach ($channels as $channel)
                    @if ($channel['active'])
                        <form action="{{ route('payment.process', [$encryptedCourseId]) }}" method="POST" target="_blank">
                            @csrf
                            <input type="hidden" name="channel" value="{{ $channel['code'] }}">
                            <input type="hidden" name="referral_code" value="{{ $referralCode }}">
                            <div
                                class="p-4 border rounded-xl flex items-center justify-between bg-white shadow-sm hover:shadow-md transition">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $channel['icon_url'] }}" alt="{{ $channel['name'] }}"
                                        class="w-12 h-12 object-contain">
                                    <div>
                                        <p class="font-semibold">{{ $channel['name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $channel['group'] }}</p>
                                    </div>
                                </div>
                                <button type="submit" class="btn-primary px-4 py-2 rounded-lg text-white">
                                    Pilih
                                </button>
                            </div>
                        </form>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endsection
