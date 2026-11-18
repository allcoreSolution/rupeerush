<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LobbySettingsController extends Controller
{
    /**
     * Complete list of the 11 official games in RupeeRush with default metadata.
     *
     * @return array<string, array{id: string, name: string, default_badge: string, route: string, category: string, icon: string}>
     */
    protected function getOfficialGames(): array
    {
        return [
            'lottery' => [
                'id' => 'lottery',
                'name' => '2 Digit Slot',
                'default_badge' => '90X',
                'route' => route('lottery.index'),
                'category' => 'Lottery & Slots',
                'icon' => 'fa-dice',
            ],
            'wingo' => [
                'id' => 'wingo',
                'name' => 'WinGo Color',
                'default_badge' => '9X',
                'route' => route('wingo.index'),
                'category' => 'Lottery & Slots',
                'icon' => 'fa-gamepad',
            ],
            'andar_bahar' => [
                'id' => 'andar_bahar',
                'name' => 'Andar Bahar',
                'default_badge' => '1.95X',
                'route' => route('andar-bahar.index'),
                'category' => 'Live Casino',
                'icon' => 'fa-diamond',
            ],
            'aviator' => [
                'id' => 'aviator',
                'name' => 'Aviator',
                'default_badge' => '1000X',
                'route' => route('aviator.index'),
                'category' => 'Crash & Arcade',
                'icon' => 'fa-plane-departure',
            ],
            'mines' => [
                'id' => 'mines',
                'name' => 'Mines',
                'default_badge' => 'ARCADE',
                'route' => route('mines.index'),
                'category' => 'Crash & Arcade',
                'icon' => 'fa-bomb',
            ],
            'vortex' => [
                'id' => 'vortex',
                'name' => 'Vortex Wheel',
                'default_badge' => '200X',
                'route' => route('vortex.index'),
                'category' => 'Crash & Arcade',
                'icon' => 'fa-arrows-spin',
            ],
            'teen_patti' => [
                'id' => 'teen_patti',
                'name' => 'Teen Patti',
                'default_badge' => '40X',
                'route' => route('teen-patti.index'),
                'category' => 'Live Casino',
                'icon' => 'fa-heart',
            ],
            'dragon_tiger' => [
                'id' => 'dragon_tiger',
                'name' => 'Dragon Tiger',
                'default_badge' => '50X',
                'route' => route('dragon-tiger.index'),
                'category' => 'Live Casino',
                'icon' => 'fa-dragon',
            ],
            'roulette' => [
                'id' => 'roulette',
                'name' => 'Roulette',
                'default_badge' => '36X',
                'route' => route('roulette.index'),
                'category' => 'Live Casino',
                'icon' => 'fa-dharmachakra',
            ],
            'horse_racing' => [
                'id' => 'horse_racing',
                'name' => 'Horse Racing',
                'default_badge' => 'DERBY',
                'route' => route('horse-racing.index'),
                'category' => 'Sports & Derby',
                'icon' => 'fa-horse-head',
            ],
            'cricket' => [
                'id' => 'cricket',
                'name' => 'Cricket Sports',
                'default_badge' => 'LIVE',
                'route' => route('sports.cricket'),
                'category' => 'Cricket Exchange',
                'icon' => 'fa-baseball-bat-ball',
            ],
        ];
    }

    /**
     * Display the dedicated Lobby Banners & Game Cards management hub.
     */
    public function index(): View
    {
        $setting = AdminSetting::current();
        $games = $this->getOfficialGames();
        $banners = $setting->getHeroBannersList();

        return view('admin.lobby.index', [
            'setting' => $setting,
            'games' => $games,
            'banners' => $banners,
        ]);
    }

    /**
     * Update all 6 Hero Slider Carousel Banners (16:9 ratio).
     */
    public function updateBanners(Request $request): RedirectResponse
    {
        $setting = AdminSetting::current();

        $rules = [];
        for ($i = 1; $i <= 6; $i++) {
            $rules["hero_banner_{$i}"] = ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:5120'];
            $rules["hero_banner_{$i}_link"] = ['nullable', 'string', 'max:255'];
            $rules["hero_banner_{$i}_title"] = ['nullable', 'string', 'max:100'];
            $rules["hero_banner_{$i}_subtitle"] = ['nullable', 'string', 'max:150'];
        }

        $validated = $request->validate($rules);

        $bannersDir = public_path('uploads/banners');
        if (! file_exists($bannersDir)) {
            mkdir($bannersDir, 0755, true);
        }
        $storageBannersDir = storage_path('app/public/banners');
        if (! file_exists($storageBannersDir)) {
            mkdir($storageBannersDir, 0755, true);
        }

        $titles = $setting->hero_banner_titles ?? [];

        for ($i = 1; $i <= 6; $i++) {
            $bannerFileKey = "hero_banner_{$i}";
            $bannerLinkKey = "hero_banner_{$i}_link";
            $removeKey = "remove_banner_{$i}";

            if ($request->boolean($removeKey)) {
                if (! empty($setting->{$bannerFileKey})) {
                    @unlink(public_path($setting->{$bannerFileKey}));
                    @unlink($storageBannersDir.DIRECTORY_SEPARATOR.basename($setting->{$bannerFileKey}));
                }
                $setting->{$bannerFileKey} = null;
            } elseif ($request->hasFile($bannerFileKey)) {
                $file = $request->file($bannerFileKey);
                $extension = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = "hero_banner_{$i}_".time().'_'.uniqid().'.'.$extension;

                $file->move($bannersDir, $filename);
                @copy($bannersDir.DIRECTORY_SEPARATOR.$filename, $storageBannersDir.DIRECTORY_SEPARATOR.$filename);

                if (! empty($setting->{$bannerFileKey})) {
                    @unlink(public_path($setting->{$bannerFileKey}));
                    @unlink($storageBannersDir.DIRECTORY_SEPARATOR.basename($setting->{$bannerFileKey}));
                }

                $setting->{$bannerFileKey} = 'uploads/banners/'.$filename;
            }

            if (isset($validated[$bannerLinkKey])) {
                $setting->{$bannerLinkKey} = trim($validated[$bannerLinkKey]);
            }

            // Custom title & subtitle
            $customTitle = trim($request->input("hero_banner_{$i}_title", ''));
            $customSub = trim($request->input("hero_banner_{$i}_subtitle", ''));
            $titles[$i] = [
                'title' => $customTitle ?: null,
                'subtitle' => $customSub ?: null,
            ];
        }

        $setting->hero_banner_titles = $titles;
        $setting->save();

        return redirect()->route('admin.lobby.index')
            ->with('success', 'Hero Carousel Banners (16:9) updated successfully! Changes are live on the gaming lobby.');
    }

    /**
     * Update Game Cards Thumbnails, Custom Badges, and Titles for all 11 games.
     */
    public function updateCards(Request $request): RedirectResponse
    {
        $setting = AdminSetting::current();
        $games = $this->getOfficialGames();

        $rules = [];
        foreach (array_keys($games) as $gId) {
            $rules["card_img_{$gId}"] = ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'];
            $rules["card_badge_{$gId}"] = ['nullable', 'string', 'max:20'];
            $rules["card_title_{$gId}"] = ['nullable', 'string', 'max:50'];
        }

        $request->validate($rules);

        $gamesDir = public_path('uploads/games');
        if (! file_exists($gamesDir)) {
            mkdir($gamesDir, 0755, true);
        }
        $storageGamesDir = storage_path('app/public/games');
        if (! file_exists($storageGamesDir)) {
            mkdir($storageGamesDir, 0755, true);
        }

        $cardsConfig = $setting->game_cards_config ?? [];

        foreach (array_keys($games) as $gId) {
            $imgKey = "card_img_{$gId}";
            $removeKey = "remove_card_{$gId}";
            $badgeKey = "card_badge_{$gId}";
            $titleKey = "card_title_{$gId}";

            if (! isset($cardsConfig[$gId])) {
                $cardsConfig[$gId] = [];
            }

            if ($request->boolean($removeKey)) {
                if (! empty($cardsConfig[$gId]['image'])) {
                    @unlink(public_path($cardsConfig[$gId]['image']));
                    @unlink($storageGamesDir.DIRECTORY_SEPARATOR.basename($cardsConfig[$gId]['image']));
                }
                unset($cardsConfig[$gId]['image']);
            } elseif ($request->hasFile($imgKey)) {
                $file = $request->file($imgKey);
                $ext = $file->getClientOriginalExtension() ?: 'png';
                $filename = "game_{$gId}_".time().'_'.uniqid().'.'.$ext;

                $file->move($gamesDir, $filename);
                @copy($gamesDir.DIRECTORY_SEPARATOR.$filename, $storageGamesDir.DIRECTORY_SEPARATOR.$filename);

                if (! empty($cardsConfig[$gId]['image'])) {
                    @unlink(public_path($cardsConfig[$gId]['image']));
                    @unlink($storageGamesDir.DIRECTORY_SEPARATOR.basename($cardsConfig[$gId]['image']));
                }

                $cardsConfig[$gId]['image'] = 'uploads/games/'.$filename;
            }

            // Custom Badge
            $badgeVal = trim($request->input($badgeKey, ''));
            if ($badgeVal !== '') {
                $cardsConfig[$gId]['badge'] = $badgeVal;
            } else {
                unset($cardsConfig[$gId]['badge']);
            }

            // Custom Title
            $titleVal = trim($request->input($titleKey, ''));
            if ($titleVal !== '') {
                $cardsConfig[$gId]['title'] = $titleVal;
            } else {
                unset($cardsConfig[$gId]['title']);
            }
        }

        $setting->game_cards_config = $cardsConfig;
        $setting->save();

        return redirect()->route('admin.lobby.index')
            ->with('success', 'Game Card Thumbnails & Badges updated successfully! Changes are live on the gaming lobby.');
    }
}
