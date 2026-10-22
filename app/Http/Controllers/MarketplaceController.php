<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    /**
     * Original price list mapping for strikethrough prices.
     */
    protected array $originalPrices = [
        'Makalah (Tanpa Materi)' => 140000,
        'Makalah (Ada Materi)' => 98000,
        'Jurnal Ilmiah' => 182000,
        'CV Kreatif' => 35000,
        'CV ATS-Friendly' => 84000,
        'Surat Lamaran Kerja' => 28000,
        'Gabung & Rapikan PDF' => 14000,
        'Website Landing Page' => 2000000,
        'Web Operasional & POS' => 10000000,
        'Desain Grafis & Promo' => 140000,
        'Data Entry & Spreadsheet' => 210000,
        'Jasa Ketik Word' => 140000,
        'Jasa Excel & Analisis Data' => 182000,
    ];

    /**
     * Display the marketplace landing page.
     */
    public function index(): View
    {
        $products = Product::where('is_active', true)
            ->where('is_trending', true)
            ->get()
            ->map(function ($product) {
                $product->original_price = $this->originalPrices[$product->name] ?? null;
                return $product;
            });

        $brands = Brand::where('is_active', true)
            ->get()
            ->keyBy('slug');

        $quickBrandConfigs = [
            'netflix' => ['name' => 'Netflix', 'logo' => 'assets/logo/Netflix_2015_logo.svg'],
            'chatgpt' => ['name' => 'ChatGPT', 'logo' => 'assets/logo/OpenAI_logo_2025_(symbol).svg'],
            'claude' => ['name' => 'Claude', 'logo' => 'assets/logo/Claude_AI_logo.svg'],
            'gemini' => ['name' => 'Gemini', 'logo' => 'assets/logo/Google_Gemini_logo.svg'],
            'spotify' => ['name' => 'Spotify', 'logo' => 'assets/logo/2024_Spotify_Logo.svg'],
            'canva' => ['name' => 'Canva', 'logo' => 'assets/logo/Canva_logo.svg'],
            'instagram' => ['name' => 'Instagram', 'logo' => 'assets/logo/Instagram_logo_2022.svg'],
            'copilot' => ['name' => 'Copilot', 'logo' => 'assets/logo/Visual_Studio_Code_1.35_icon.svg'],
            'figma' => ['name' => 'Figma', 'logo' => 'assets/logo/figma-logo.svg'],
        ];

        $quickBrands = collect($quickBrandConfigs)->map(function ($cfg, $slug) use ($brands) {
            $brandItem = $brands->get($slug);
            return (object) [
                'slug' => $slug,
                'name' => $cfg['name'],
                'logo' => ($brandItem && $brandItem->logo) ? asset($brandItem->logo) : asset($cfg['logo']),
                'is_out_of_stock' => $brandItem ? ($brandItem->stock <= 0) : false,
            ];
        });

        return view('page.marketplace', compact('products', 'brands', 'quickBrands'));
    }

    /**
     * Display trending products & brands page.
     */
    public function trending(): View
    {
        $trendingProducts = Product::where('is_active', true)
            ->where('is_trending', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        $trendingBrands = Brand::where('is_active', true)
            ->where('is_trending', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('page.trending', compact('trendingProducts', 'trendingBrands'));
    }

    /**
     * Display new arrival products & brands page.
     */
    public function newArrival(): View
    {
        $newProducts = Product::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $newBrands = Brand::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('page.new-arrival', compact('newProducts', 'newBrands'));
    }

    /**
     * Display all products catalog page.
     */
    public function allProducts(): View
    {
        $products = Product::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        $categories = Product::where('is_active', true)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view('page.all-product', compact('products', 'categories'));
    }

    /**
     * Display brand detail page.
     */
    public function brandDetail(string $slug): View
    {
        $brand = Brand::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('page.brand-detail', compact('brand', 'slug'));
    }

    /**
     * Display product detail page with price calculation and related products.
     */
    public function productDetail(Product $product): View
    {
        if (!$product->is_active) {
            abort(404);
        }

        $originalPrices = [
            'Makalah (Tanpa Materi)' => 140000,
            'Makalah (Ada Materi)' => 98000,
            'Jurnal Ilmiah' => 182000,
            'CV Kreatif' => 35000,
            'CV ATS-Friendly' => 84000,
            'Surat Lamaran Kerja' => 28000,
            'Gabung & Rapikan PDF' => 14000,
            'Website Landing Page' => 2000000,
            'Web Operasional & POS' => 10000000,
            'Desain Grafis & Promo' => 140000,
            'Data Entry & Spreadsheet' => 210000,
            'Jasa Ketik Word' => 140000,
            'Jasa Excel & Analisis Data' => 182000,
        ];
        $originalPrice = $originalPrices[$product->name] ?? null;

        $relatedProducts = Product::where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                $q->where('category', $product->category)
                  ->orWhere('is_trending', true);
            })
            ->take(4)
            ->get();

        return view('page.product-detail', compact('product', 'originalPrice', 'relatedProducts'));
    }
}
