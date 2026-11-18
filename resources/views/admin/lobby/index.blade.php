@extends('layouts.admin')

@section('title', 'Lobby Banners & Game Cards')

@section('content')
<div class="container-fluid px-0">
    <!-- Top Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-photo-film text-warning"></i>
                <span>Lobby Banners &amp; Game Cards Hub</span>
            </h3>
            <p class="text-muted small mb-0">
                Manage 16:9 Hero Carousel Sliders and custom Game Card thumbnails for the main gaming lobby.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill fw-bold px-3">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Live Lobby
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4 d-flex align-items-center gap-2" role="alert">
            <i class="fa-solid fa-circle-check fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i> Please fix the following errors:
            </div>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Nav Tabs: Slider Banners & Game Cards -->
    <ul class="nav nav-pills nav-fill gap-2 p-1 bg-white rounded-4 border shadow-sm mb-4" id="lobbySettingsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill fw-bold py-2 d-flex align-items-center justify-content-center gap-2" id="sliders-tab" data-bs-toggle="pill" data-bs-target="#sliders-pane" type="button" role="tab" aria-controls="sliders-pane" aria-selected="true">
                <i class="fa-solid fa-sliders text-warning"></i>
                <span>Hero Carousel Sliders (16:9)</span>
                <span class="badge bg-warning text-dark rounded-pill px-2">6 Slides</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-bold py-2 d-flex align-items-center justify-content-center gap-2" id="cards-tab" data-bs-toggle="pill" data-bs-target="#cards-pane" type="button" role="tab" aria-controls="cards-pane" aria-selected="false">
                <i class="fa-solid fa-gamepad text-primary"></i>
                <span>Game Cards Thumbnails</span>
                <span class="badge bg-primary text-white rounded-pill px-2">11 Games</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="lobbySettingsTabsContent">
        <!-- ========================================================
             TAB 1: HERO CAROUSEL SLIDERS (16:9)
             ======================================================== -->
        <div class="tab-pane fade show active" id="sliders-pane" role="tabpanel" aria-labelledby="sliders-tab" tabindex="0">
            <form action="{{ route('admin.lobby.banners.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Ratio Notice Alert -->
                <div class="alert alert-warning-subtle border border-warning-subtle rounded-4 p-3 mb-4 d-flex align-items-start gap-3">
                    <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-expand"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">Native 16:9 Widescreen Slider Ratio</div>
                        <div class="small text-muted">
                            All slider images are now rendered in pure <strong>16:9 ratio</strong>. Upload widescreen images (Recommended: <strong>1280×720 px</strong> or <strong>1920×1080 px</strong>, max 5MB). Images will automatically fit edge-to-edge without any distortion or awkward cropping!
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach($banners as $banner)
                        @php
                            $i = $banner['index'];
                            $customTitle = $setting->hero_banner_titles[$i]['title'] ?? '';
                            $customSub = $setting->hero_banner_titles[$i]['subtitle'] ?? '';
                            $linkVal = $setting->{"hero_banner_{$i}_link"} ?? $banner['link'];
                        @endphp
                        <div class="col-12 col-xl-6">
                            <div class="card card-custom h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                                <div class="card-custom-header bg-light d-flex align-items-center justify-content-between py-3 px-4 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-dark rounded-pill px-3 py-1 fw-bold">Slide #{{ $i }}</span>
                                        <span class="fw-bold text-dark">{{ $banner['title'] }}</span>
                                    </div>
                                    @if($banner['is_custom'])
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                            <i class="fa-solid fa-circle-check me-1"></i> Custom 16:9 Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-muted border rounded-pill px-2 py-1 small">
                                            <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Default Banner
                                        </span>
                                    @endif
                                </div>
                                <div class="card-body p-4">
                                    <!-- 16:9 Image Preview -->
                                    <div class="position-relative rounded-3 overflow-hidden border shadow-sm mb-3 bg-dark" style="aspect-ratio: 16 / 9; width: 100%;">
                                        <img id="bannerPreviewImg_{{ $i }}" src="{{ $banner['image_url'] }}" alt="Banner {{ $i }}" class="w-100 h-100 object-fit-cover" style="aspect-ratio: 16 / 9;">
                                        <div class="position-absolute bottom-0 start-0 end-0 p-2 text-white bg-dark bg-opacity-75 small d-flex align-items-center justify-content-between">
                                            <span class="fw-bold"><i class="fa-solid fa-ratio me-1"></i> 16:9 Preview</span>
                                            <span class="font-monospace small text-warning">Slide {{ $i }}</span>
                                        </div>
                                    </div>

                                    <!-- Upload Input -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted mb-1">
                                            <i class="fa-solid fa-upload me-1 text-primary"></i> Upload 16:9 Banner Image (JPG, PNG, WEBP, SVG)
                                        </label>
                                        <input type="file" class="form-control form-control-sm" name="hero_banner_{{ $i }}" accept="image/*" onchange="previewLobbyBanner(this, {{ $i }})">
                                    </div>

                                    <!-- Click Link -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted mb-1">
                                            <i class="fa-solid fa-link me-1 text-info"></i> Target Click URL (Game Route / Custom Link)
                                        </label>
                                        <input type="text" class="form-control form-control-sm font-monospace" name="hero_banner_{{ $i }}_link" value="{{ old('hero_banner_'.$i.'_link', $linkVal) }}" placeholder="e.g. {{ $banner['link'] }}">
                                    </div>

                                    <!-- Title & Subtitle -->
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-muted mb-1">Display Title (Optional)</label>
                                            <input type="text" class="form-control form-control-sm" name="hero_banner_{{ $i }}_title" value="{{ old('hero_banner_'.$i.'_title', $customTitle) }}" placeholder="{{ $banner['title'] }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-muted mb-1">Subtitle / Tagline (Optional)</label>
                                            <input type="text" class="form-control form-control-sm" name="hero_banner_{{ $i }}_subtitle" value="{{ old('hero_banner_'.$i.'_subtitle', $customSub) }}" placeholder="{{ $banner['subtitle'] }}">
                                        </div>
                                    </div>

                                    @if($banner['is_custom'])
                                        <div class="form-check mt-3 pt-2 border-top">
                                            <input class="form-check-input text-danger" type="checkbox" name="remove_banner_{{ $i }}" id="removeBanner_{{ $i }}" value="1">
                                            <label class="form-check-label small text-danger fw-bold" for="removeBanner_{{ $i }}">
                                                <i class="fa-solid fa-trash-can me-1"></i> Remove custom image &amp; reset to default
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                    <button type="submit" class="btn btn-warning rounded-pill px-5 py-2 fw-bold shadow-sm d-flex align-items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save All 6 Sliders</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ========================================================
             TAB 2: GAME CARDS THUMBNAILS (11 GAMES)
             ======================================================== -->
        <div class="tab-pane fade" id="cards-pane" role="tabpanel" aria-labelledby="cards-tab" tabindex="0">
            <form action="{{ route('admin.lobby.cards.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Info Notice Alert -->
                <div class="alert alert-primary-subtle border border-primary-subtle rounded-4 p-3 mb-4 d-flex align-items-start gap-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-gamepad"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">Custom Game Card Covers &amp; Badges</div>
                        <div class="small text-muted">
                            Upload high quality game cover artwork for any of the 11 games. Recommended card size: <strong>400×300 px</strong> or <strong>500×350 px</strong> (Aspect ratio ~4:3). When a custom image is uploaded, it covers the card dynamically while preserving the bottom title and top multiplier badge!
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach($games as $gId => $game)
                        @php
                            $cardImgUrl = $setting->getGameCardImage($gId);
                            $cardMeta = $setting->getGameCardMeta($gId);
                            $hasCustomImg = !empty($cardMeta['image']);
                            $customBadge = $cardMeta['badge'] ?? '';
                            $customTitle = $cardMeta['title'] ?? '';
                        @endphp
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="card card-custom h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                                <!-- Card Header -->
                                <div class="card-custom-header bg-light py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid {{ $game['icon'] }} text-primary"></i>
                                        <span class="fw-bold text-dark text-truncate" style="max-width: 140px;">{{ $game['name'] }}</span>
                                    </div>
                                    <span class="badge bg-secondary-subtle text-dark border px-2 py-0 small" style="font-size: 0.65rem;">
                                        {{ $game['category'] }}
                                    </span>
                                </div>

                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                    <!-- Live Preview Mockup -->
                                    <div class="mb-3">
                                        <div class="position-relative rounded-3 overflow-hidden border shadow-sm mx-auto" style="aspect-ratio: 1.35 / 1; width: 100%; max-width: 240px; background: linear-gradient(135deg, #090e1a 0%, #1e293b 100%);">
                                            @if($cardImgUrl)
                                                <img id="cardPreviewImg_{{ $gId }}" src="{{ $cardImgUrl }}" alt="{{ $game['name'] }}" class="w-100 h-100 object-fit-cover">
                                            @else
                                                <div id="cardPreviewPlaceholder_{{ $gId }}" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                                                    <i class="fa-solid {{ $game['icon'] }} fs-1 mb-1 text-warning opacity-75"></i>
                                                    <span class="small" style="font-size: 0.7rem;">Default 3D Icon</span>
                                                </div>
                                                <img id="cardPreviewImg_{{ $gId }}" src="" alt="{{ $game['name'] }}" class="w-100 h-100 object-fit-cover d-none">
                                            @endif

                                            <!-- Preview Corner Badge -->
                                            <span class="position-absolute top-0 end-0 m-1 badge bg-danger text-white rounded-pill px-2 py-0" style="font-size: 0.65rem;" id="badgePreview_{{ $gId }}">
                                                {{ $customBadge ?: $game['default_badge'] }}
                                            </span>

                                            <!-- Preview Title Banner -->
                                            <div class="position-absolute bottom-0 start-0 end-0 p-1 px-2 text-white bg-dark bg-opacity-80 small text-truncate fw-bold" style="font-size: 0.72rem;" id="titlePreview_{{ $gId }}">
                                                {{ $customTitle ?: $game['name'] }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Controls -->
                                    <div>
                                        <div class="mb-2">
                                            <label class="form-label fw-bold text-muted small mb-1" style="font-size: 0.72rem;">
                                                <i class="fa-solid fa-upload me-1 text-primary"></i> Upload Card Cover Image
                                            </label>
                                            <input type="file" class="form-control form-control-sm" name="card_img_{{ $gId }}" accept="image/*" onchange="previewGameCard(this, '{{ $gId }}')">
                                        </div>

                                        <div class="row g-2 mb-2">
                                            <div class="col-7">
                                                <label class="form-label fw-bold text-muted small mb-1" style="font-size: 0.72rem;">Card Title</label>
                                                <input type="text" class="form-control form-control-sm" name="card_title_{{ $gId }}" value="{{ old('card_title_'.$gId, $customTitle) }}" placeholder="{{ $game['name'] }}" oninput="updateTitlePreview('{{ $gId }}', this.value, '{{ $game['name'] }}')">
                                            </div>
                                            <div class="col-5">
                                                <label class="form-label fw-bold text-muted small mb-1" style="font-size: 0.72rem;">Badge Text</label>
                                                <input type="text" class="form-control form-control-sm font-monospace text-uppercase" name="card_badge_{{ $gId }}" value="{{ old('card_badge_'.$gId, $customBadge) }}" placeholder="{{ $game['default_badge'] }}" oninput="updateBadgePreview('{{ $gId }}', this.value, '{{ $game['default_badge'] }}')">
                                            </div>
                                        </div>

                                        @if($hasCustomImg)
                                            <div class="form-check pt-2 border-top">
                                                <input class="form-check-input text-danger" type="checkbox" name="remove_card_{{ $gId }}" id="removeCard_{{ $gId }}" value="1">
                                                <label class="form-check-label small text-danger fw-bold" style="font-size: 0.72rem;" for="removeCard_{{ $gId }}">
                                                    <i class="fa-solid fa-trash-can me-1"></i> Reset to Default Icon
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm d-flex align-items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Game Cards</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Live preview for Hero Banners
    function previewLobbyBanner(input, index) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('bannerPreviewImg_' + index);
                if (img) {
                    img.src = e.target.result;
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Live preview for Game Cards
    function previewGameCard(input, gameId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('cardPreviewImg_' + gameId);
                const placeholder = document.getElementById('cardPreviewPlaceholder_' + gameId);
                if (img) {
                    img.src = e.target.result;
                    img.classList.remove('d-none');
                }
                if (placeholder) {
                    placeholder.classList.add('d-none');
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function updateTitlePreview(gameId, value, defaultVal) {
        const el = document.getElementById('titlePreview_' + gameId);
        if (el) {
            el.textContent = value.trim() ? value.trim() : defaultVal;
        }
    }

    function updateBadgePreview(gameId, value, defaultVal) {
        const el = document.getElementById('badgePreview_' + gameId);
        if (el) {
            el.textContent = value.trim() ? value.trim() : defaultVal;
        }
    }
</script>
@endsection
