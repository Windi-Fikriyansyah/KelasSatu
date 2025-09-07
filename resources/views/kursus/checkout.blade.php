@extends('layouts.app')
@section('title', 'Checkout - ' . $course->title)
@section('content')
    <section class="py-20 bg-primary-50">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-2xl shadow p-6">
                <h2 class="text-2xl font-bold text-primary-200 mb-4">Checkout</h2>

                <p class="text-gray-700 mb-4">Kursus: <strong>{{ $course->title }}</strong></p>
                <p class="text-gray-700 mb-4">Harga: <strong>Rp {{ number_format($course->price, 0, ',', '.') }}</strong></p>

                <form action="{{ route('course.pay', $course->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-primary px-6 py-3 rounded-lg text-white font-semibold">
                        Bayar Sekarang
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
