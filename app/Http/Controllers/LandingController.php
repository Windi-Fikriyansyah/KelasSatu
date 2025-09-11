<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class LandingController extends Controller
{
    public function index()
    {
        $landing = DB::table('landing_page_sections')->first();

        if ($landing) {
            $landing->features = DB::table('landing_page_features')
                ->where('landing_page_id', $landing->id)
                ->orderBy('order')
                ->get();

            $landing->testimonials = DB::table('landing_page_testimonials')
                ->where('landing_page_id', $landing->id)
                ->orderBy('order')
                ->get();

            $landing->faqs = DB::table('landing_page_faqs')
                ->where('landing_page_id', $landing->id)
                ->orderBy('order')
                ->get();
        }

        return view('landing.index', compact('landing'));
    }

    public function store(Request $request)
    {
        return $this->saveOrUpdate($request);
    }

    public function update(Request $request, $id)
    {
        return $this->saveOrUpdate($request, $id);
    }

    private function saveOrUpdate(Request $request, $id = null)
    {
        DB::beginTransaction();

        try {
            // Handle file uploads
            $data = $request->except(['_token', '_method', 'features', 'testimonials', 'faqs']);

            // Process image uploads
            $imageFields = [
                'hero_image_1',
                'hero_image_2',
                'hero_image_3',
                'about_image'
            ];

            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('landing', $fileName, 'public');
                    $data[$field] = $path;
                } elseif ($id && !$request->has($field . '_remove')) {
                    // Keep existing file if not removed
                    $existing = DB::table('landing_page_sections')->where('id', $id)->first();
                    $data[$field] = $existing->$field ?? null;
                } else {
                    $data[$field] = null;
                }
            }

            // Save or update main section
            if ($id) {
                DB::table('landing_page_sections')
                    ->where('id', $id)
                    ->update($data);
                $landingId = $id;
            } else {
                $landingId = DB::table('landing_page_sections')->insertGetId($data);
            }

            // Process features
            if ($request->has('features')) {
                DB::table('landing_page_features')
                    ->where('landing_page_id', $landingId)
                    ->delete();

                foreach ($request->features as $index => $feature) {
                    if (!empty($feature['title']) && !empty($feature['description'])) {
                        DB::table('landing_page_features')->insert([
                            'landing_page_id' => $landingId,
                            'order' => $index,
                            'title' => $feature['title'],
                            'description' => $feature['description'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            // Process testimonials
            if ($request->has('testimonials')) {
                DB::table('landing_page_testimonials')
                    ->where('landing_page_id', $landingId)
                    ->delete();

                foreach ($request->testimonials as $index => $testimonial) {
                    if (!empty($testimonial['name']) && !empty($testimonial['content'])) {
                        DB::table('landing_page_testimonials')->insert([
                            'landing_page_id' => $landingId,
                            'order' => $index,
                            'name' => $testimonial['name'],
                            'role' => $testimonial['role'] ?? '',
                            'content' => $testimonial['content'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            // Process FAQs
            if ($request->has('faqs')) {
                DB::table('landing_page_faqs')
                    ->where('landing_page_id', $landingId)
                    ->delete();

                foreach ($request->faqs as $index => $faq) {
                    if (!empty($faq['question']) && !empty($faq['answer'])) {
                        DB::table('landing_page_faqs')->insert([
                            'landing_page_id' => $landingId,
                            'order' => $index,
                            'question' => $faq['question'],
                            'answer' => $faq['answer'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('landing.index')
                ->with('success', 'Pengaturan landing page berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
}
