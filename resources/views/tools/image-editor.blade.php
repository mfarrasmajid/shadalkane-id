@extends('layouts.app')

@section('title', 'Image Editor - ShadAlkane Tools')

@section('styles')
<style>
    .editor-container {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 20px;
        min-height: calc(100vh - 120px);
    }

    /* Sidebar / Toolbar */
    .editor-sidebar {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 20px;
        padding: 20px;
        overflow-y: auto;
        max-height: calc(100vh - 120px);
    }

    .sidebar-section {
        margin-bottom: 24px;
    }

    .sidebar-section h4 {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgba(255, 255, 255, 0.5);
        margin-bottom: 12px;
    }

    .tool-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .tool-btn {
        padding: 10px 8px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.05);
        color: rgba(255, 255, 255, 0.8);
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Inter', sans-serif;
        font-size: 0.8rem;
        font-weight: 500;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }

    .tool-btn i {
        font-size: 1.1rem;
    }

    .tool-btn:hover {
        background: rgba(220, 38, 38, 0.2);
        border-color: rgba(220, 38, 38, 0.4);
        color: #fff;
    }

    .tool-btn.active {
        background: rgba(220, 38, 38, 0.4);
        border-color: rgba(220, 38, 38, 0.6);
        color: #fff;
    }

    /* Slider controls */
    .slider-group {
        margin-bottom: 16px;
    }

    .slider-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .slider-label {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.7);
        font-weight: 500;
    }

    .slider-value {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.5);
        font-weight: 600;
    }

    input[type="range"] {
        width: 100%;
        height: 6px;
        -webkit-appearance: none;
        appearance: none;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 3px;
        outline: none;
    }

    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: linear-gradient(135deg, #DC2626, #7C3AED);
        cursor: pointer;
        border: 2px solid #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }

    /* Canvas area */
    .editor-main {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .editor-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        flex-wrap: wrap;
        gap: 8px;
    }

    .editor-topbar .actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .canvas-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
        overflow: auto;
        background:
            linear-gradient(45deg, rgba(255,255,255,0.03) 25%, transparent 25%),
            linear-gradient(-45deg, rgba(255,255,255,0.03) 25%, transparent 25%),
            linear-gradient(45deg, transparent 75%, rgba(255,255,255,0.03) 75%),
            linear-gradient(-45deg, transparent 75%, rgba(255,255,255,0.03) 75%);
        background-size: 20px 20px;
        background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
    }

    #imageCanvas {
        max-width: 100%;
        max-height: 100%;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        cursor: crosshair;
    }

    /* Upload area */
    .upload-area {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 400px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .upload-area:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    .upload-icon {
        width: 100px;
        height: 100px;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: rgba(255, 255, 255, 0.3);
        margin-bottom: 20px;
        border: 2px dashed rgba(255, 255, 255, 0.15);
    }

    .upload-area h3 {
        font-size: 1.2rem;
        margin-bottom: 8px;
        color: rgba(255, 255, 255, 0.8);
    }

    .upload-area p {
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.9rem;
    }

    /* Crop overlay */
    .crop-overlay {
        position: absolute;
        border: 2px dashed #fff;
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5);
        cursor: move;
        display: none;
    }

    .crop-overlay.show {
        display: block;
    }

    .crop-handle {
        position: absolute;
        width: 12px;
        height: 12px;
        background: #fff;
        border-radius: 2px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .crop-handle.nw { top: -6px; left: -6px; cursor: nw-resize; }
    .crop-handle.ne { top: -6px; right: -6px; cursor: ne-resize; }
    .crop-handle.sw { bottom: -6px; left: -6px; cursor: sw-resize; }
    .crop-handle.se { bottom: -6px; right: -6px; cursor: se-resize; }

    /* Resize modal */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(10px);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-card {
        background: rgba(30, 30, 30, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        padding: 32px;
        width: 100%;
        max-width: 400px;
        margin: 20px;
    }

    .modal-card h3 {
        font-size: 1.2rem;
        margin-bottom: 20px;
    }

    .modal-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }

    .modal-actions button {
        flex: 1;
    }

    .image-info {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.5);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    @media (max-width: 768px) {
        .editor-container {
            grid-template-columns: 1fr;
        }

        .editor-sidebar {
            max-height: none;
        }

        .tool-buttons {
            grid-template-columns: repeat(4, 1fr);
        }
    }
</style>
@endsection

@section('content')
<h1 class="page-title">
    <i class="fas fa-image"></i> Image Editor
</h1>
<p class="page-subtitle">Upload, edit, dan download gambar dengan mudah.</p>

<div class="editor-container">
    <!-- Sidebar -->
    <div class="editor-sidebar" id="editorSidebar" style="display:none;">
        <div class="sidebar-section">
            <h4>Tools</h4>
            <div class="tool-buttons">
                <button class="tool-btn" onclick="activateTool('crop')" id="cropBtn">
                    <i class="fas fa-crop-alt"></i> Crop
                </button>
                <button class="tool-btn" onclick="openResizeModal()" id="resizeBtn">
                    <i class="fas fa-expand-arrows-alt"></i> Resize
                </button>
                <button class="tool-btn" onclick="rotateImage(90)">
                    <i class="fas fa-redo"></i> Rotate R
                </button>
                <button class="tool-btn" onclick="rotateImage(-90)">
                    <i class="fas fa-undo"></i> Rotate L
                </button>
                <button class="tool-btn" onclick="flipImage('h')">
                    <i class="fas fa-arrows-alt-h"></i> Flip H
                </button>
                <button class="tool-btn" onclick="flipImage('v')">
                    <i class="fas fa-arrows-alt-v"></i> Flip V
                </button>
            </div>
        </div>

        <div class="sidebar-section">
            <h4>Adjustments</h4>

            <div class="slider-group">
                <div class="slider-header">
                    <span class="slider-label">Brightness</span>
                    <span class="slider-value" id="brightnessVal">100%</span>
                </div>
                <input type="range" id="brightness" min="0" max="200" value="100" oninput="applyFilters()">
            </div>

            <div class="slider-group">
                <div class="slider-header">
                    <span class="slider-label">Contrast</span>
                    <span class="slider-value" id="contrastVal">100%</span>
                </div>
                <input type="range" id="contrast" min="0" max="200" value="100" oninput="applyFilters()">
            </div>

            <div class="slider-group">
                <div class="slider-header">
                    <span class="slider-label">Saturation</span>
                    <span class="slider-value" id="saturationVal">100%</span>
                </div>
                <input type="range" id="saturation" min="0" max="200" value="100" oninput="applyFilters()">
            </div>

            <div class="slider-group">
                <div class="slider-header">
                    <span class="slider-label">Blur</span>
                    <span class="slider-value" id="blurVal">0px</span>
                </div>
                <input type="range" id="blur" min="0" max="20" value="0" oninput="applyFilters()">
            </div>

            <div class="slider-group">
                <div class="slider-header">
                    <span class="slider-label">Grayscale</span>
                    <span class="slider-value" id="grayscaleVal">0%</span>
                </div>
                <input type="range" id="grayscale" min="0" max="100" value="0" oninput="applyFilters()">
            </div>

            <div class="slider-group">
                <div class="slider-header">
                    <span class="slider-label">Sepia</span>
                    <span class="slider-value" id="sepiaVal">0%</span>
                </div>
                <input type="range" id="sepia" min="0" max="100" value="0" oninput="applyFilters()">
            </div>

            <div class="slider-group">
                <div class="slider-header">
                    <span class="slider-label">Hue Rotate</span>
                    <span class="slider-value" id="hueVal">0°</span>
                </div>
                <input type="range" id="hue" min="0" max="360" value="0" oninput="applyFilters()">
            </div>
        </div>

        <div class="sidebar-section">
            <h4>Quick Filters</h4>
            <div class="tool-buttons">
                <button class="tool-btn" onclick="applyPreset('vintage')">
                    <i class="fas fa-palette"></i> Vintage
                </button>
                <button class="tool-btn" onclick="applyPreset('cool')">
                    <i class="fas fa-snowflake"></i> Cool
                </button>
                <button class="tool-btn" onclick="applyPreset('warm')">
                    <i class="fas fa-sun"></i> Warm
                </button>
                <button class="tool-btn" onclick="applyPreset('bw')">
                    <i class="fas fa-adjust"></i> B&W
                </button>
                <button class="tool-btn" onclick="applyPreset('dramatic')">
                    <i class="fas fa-theater-masks"></i> Drama
                </button>
                <button class="tool-btn" onclick="resetFilters()">
                    <i class="fas fa-undo-alt"></i> Reset
                </button>
            </div>
        </div>

        <div class="sidebar-section">
            <h4>Background</h4>
            <div class="tool-buttons">
                <button class="tool-btn" onclick="removeBackground()" id="removeBgBtn" style="grid-column: 1 / -1;">
                    <i class="fas fa-eraser"></i> Remove Background
                </button>
            </div>
            <p style="font-size: 0.75rem; color: rgba(255,255,255,0.4); margin-top: 8px;">
                Menggunakan tolerance-based removal (klik area yang ingin dihapus)
            </p>
        </div>
    </div>

    <!-- Main editor area -->
    <div class="editor-main">
        <div class="editor-topbar" id="editorTopbar" style="display:none;">
            <div class="image-info" id="imageInfo"></div>
            <div class="actions">
                <button onclick="undoAction()" class="btn-secondary btn-sm" id="undoBtn" disabled>
                    <i class="fas fa-undo"></i> Undo
                </button>
                <label class="btn-secondary btn-sm" style="cursor:pointer; margin: 0;">
                    <i class="fas fa-folder-open"></i> Buka Lain
                    <input type="file" accept="image/*" onchange="loadNewImage(event)" style="display:none;">
                </label>
                <button onclick="downloadImage()" class="btn-primary btn-sm">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>
        </div>

        <!-- Upload area -->
        <div class="canvas-wrapper" id="uploadWrapper">
            <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click();">
                <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <h3>Upload Gambar</h3>
                <p>Klik untuk memilih atau drag & drop gambar</p>
                <p style="margin-top: 8px; font-size: 0.8rem; color: rgba(255,255,255,0.3);">JPEG, PNG, GIF, WebP - Max 10MB</p>
            </div>
        </div>

        <!-- Canvas area (hidden initially) -->
        <div class="canvas-wrapper" id="canvasWrapper" style="display:none;">
            <canvas id="imageCanvas"></canvas>
        </div>
    </div>
</div>

<input type="file" id="fileInput" accept="image/*" onchange="handleFileSelect(event)" style="display:none;">

<!-- Resize Modal -->
<div class="modal-overlay" id="resizeModal">
    <div class="modal-card">
        <h3><i class="fas fa-expand-arrows-alt"></i> Resize Gambar</h3>

        <div class="form-group">
            <label class="form-label">Lebar (px)</label>
            <input type="number" id="resizeWidth" class="form-input" min="1" max="10000">
        </div>
        <div class="form-group">
            <label class="form-label">Tinggi (px)</label>
            <input type="number" id="resizeHeight" class="form-input" min="1" max="10000">
        </div>
        <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
            <input type="checkbox" id="lockAspect" checked style="accent-color: #DC2626;">
            <label for="lockAspect" class="form-label" style="margin: 0;">Lock Aspect Ratio</label>
        </div>

        <div class="modal-actions">
            <button onclick="closeResizeModal()" class="btn-secondary">Batal</button>
            <button onclick="applyResize()" class="btn-primary">Resize</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const canvas = document.getElementById('imageCanvas');
const ctx = canvas.getContext('2d');
let originalImage = null;
let currentImage = null;
let history = [];
let activeTool = null;
let isRemovingBg = false;

// Drag and drop
const uploadArea = document.getElementById('uploadArea');
uploadArea.addEventListener('dragover', (e) => { e.preventDefault(); uploadArea.style.background = 'rgba(255,255,255,0.08)'; });
uploadArea.addEventListener('dragleave', () => { uploadArea.style.background = ''; });
uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.style.background = '';
    if (e.dataTransfer.files[0]) loadImage(e.dataTransfer.files[0]);
});

function handleFileSelect(e) {
    if (e.target.files[0]) loadImage(e.target.files[0]);
}

function loadNewImage(e) {
    if (e.target.files[0]) loadImage(e.target.files[0]);
}

function loadImage(file) {
    if (file.size > 10 * 1024 * 1024) {
        alert('File terlalu besar. Max 10MB.');
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        const img = new Image();
        img.onload = () => {
            originalImage = img;
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
            currentImage = ctx.getImageData(0, 0, canvas.width, canvas.height);
            history = [ctx.getImageData(0, 0, canvas.width, canvas.height)];

            // Show editor
            document.getElementById('uploadWrapper').style.display = 'none';
            document.getElementById('canvasWrapper').style.display = 'flex';
            document.getElementById('editorSidebar').style.display = 'block';
            document.getElementById('editorTopbar').style.display = 'flex';
            updateImageInfo();
            resetFilters();
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function updateImageInfo() {
    document.getElementById('imageInfo').innerHTML = `
        <i class="fas fa-image"></i> ${canvas.width} x ${canvas.height}px
    `;
}

function saveState() {
    history.push(ctx.getImageData(0, 0, canvas.width, canvas.height));
    if (history.length > 20) history.shift();
    document.getElementById('undoBtn').disabled = history.length <= 1;
}

function undoAction() {
    if (history.length > 1) {
        history.pop();
        const state = history[history.length - 1];
        canvas.width = state.width;
        canvas.height = state.height;
        ctx.putImageData(state, 0, 0);
        currentImage = ctx.getImageData(0, 0, canvas.width, canvas.height);
        updateImageInfo();
    }
    document.getElementById('undoBtn').disabled = history.length <= 1;
}

// Filters
function applyFilters() {
    const brightness = document.getElementById('brightness').value;
    const contrast = document.getElementById('contrast').value;
    const saturation = document.getElementById('saturation').value;
    const blur = document.getElementById('blur').value;
    const grayscale = document.getElementById('grayscale').value;
    const sepia = document.getElementById('sepia').value;
    const hue = document.getElementById('hue').value;

    document.getElementById('brightnessVal').textContent = brightness + '%';
    document.getElementById('contrastVal').textContent = contrast + '%';
    document.getElementById('saturationVal').textContent = saturation + '%';
    document.getElementById('blurVal').textContent = blur + 'px';
    document.getElementById('grayscaleVal').textContent = grayscale + '%';
    document.getElementById('sepiaVal').textContent = sepia + '%';
    document.getElementById('hueVal').textContent = hue + '°';

    canvas.style.filter = `brightness(${brightness}%) contrast(${contrast}%) saturate(${saturation}%) blur(${blur}px) grayscale(${grayscale}%) sepia(${sepia}%) hue-rotate(${hue}deg)`;
}

function resetFilters() {
    document.getElementById('brightness').value = 100;
    document.getElementById('contrast').value = 100;
    document.getElementById('saturation').value = 100;
    document.getElementById('blur').value = 0;
    document.getElementById('grayscale').value = 0;
    document.getElementById('sepia').value = 0;
    document.getElementById('hue').value = 0;
    applyFilters();
}

function applyPreset(name) {
    const presets = {
        vintage: { brightness: 110, contrast: 90, saturation: 60, blur: 0, grayscale: 20, sepia: 40, hue: 0 },
        cool: { brightness: 100, contrast: 105, saturation: 90, blur: 0, grayscale: 0, sepia: 0, hue: 200 },
        warm: { brightness: 105, contrast: 100, saturation: 130, blur: 0, grayscale: 0, sepia: 20, hue: 0 },
        bw: { brightness: 100, contrast: 120, saturation: 0, blur: 0, grayscale: 100, sepia: 0, hue: 0 },
        dramatic: { brightness: 90, contrast: 150, saturation: 120, blur: 0, grayscale: 0, sepia: 0, hue: 0 },
    };

    const p = presets[name];
    if (!p) return;

    document.getElementById('brightness').value = p.brightness;
    document.getElementById('contrast').value = p.contrast;
    document.getElementById('saturation').value = p.saturation;
    document.getElementById('blur').value = p.blur;
    document.getElementById('grayscale').value = p.grayscale;
    document.getElementById('sepia').value = p.sepia;
    document.getElementById('hue').value = p.hue;
    applyFilters();
}

// Bake filters into canvas
function bakeFilters() {
    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = canvas.width;
    tempCanvas.height = canvas.height;
    const tempCtx = tempCanvas.getContext('2d');
    tempCtx.filter = canvas.style.filter || 'none';
    tempCtx.drawImage(canvas, 0, 0);

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    canvas.style.filter = 'none';
    ctx.drawImage(tempCanvas, 0, 0);
    currentImage = ctx.getImageData(0, 0, canvas.width, canvas.height);
    resetFilters();
}

// Rotate
function rotateImage(degrees) {
    bakeFilters();
    const radians = degrees * Math.PI / 180;
    const sin = Math.abs(Math.sin(radians));
    const cos = Math.abs(Math.cos(radians));
    const newW = Math.round(canvas.width * cos + canvas.height * sin);
    const newH = Math.round(canvas.width * sin + canvas.height * cos);

    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = newW;
    tempCanvas.height = newH;
    const tempCtx = tempCanvas.getContext('2d');

    tempCtx.translate(newW / 2, newH / 2);
    tempCtx.rotate(radians);
    tempCtx.drawImage(canvas, -canvas.width / 2, -canvas.height / 2);

    canvas.width = newW;
    canvas.height = newH;
    ctx.drawImage(tempCanvas, 0, 0);
    currentImage = ctx.getImageData(0, 0, canvas.width, canvas.height);
    saveState();
    updateImageInfo();
}

// Flip
function flipImage(direction) {
    bakeFilters();
    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = canvas.width;
    tempCanvas.height = canvas.height;
    const tempCtx = tempCanvas.getContext('2d');

    if (direction === 'h') {
        tempCtx.translate(canvas.width, 0);
        tempCtx.scale(-1, 1);
    } else {
        tempCtx.translate(0, canvas.height);
        tempCtx.scale(1, -1);
    }

    tempCtx.drawImage(canvas, 0, 0);
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(tempCanvas, 0, 0);
    currentImage = ctx.getImageData(0, 0, canvas.width, canvas.height);
    saveState();
}

// Crop
let cropMode = false;
let cropStart = null;
let cropEnd = null;
let isCropping = false;

function activateTool(tool) {
    if (tool === 'crop') {
        cropMode = !cropMode;
        document.getElementById('cropBtn').classList.toggle('active', cropMode);

        if (cropMode) {
            canvas.style.cursor = 'crosshair';
            // Add crop listeners
            canvas.addEventListener('mousedown', startCrop);
            canvas.addEventListener('mousemove', moveCrop);
            canvas.addEventListener('mouseup', endCrop);
        } else {
            canvas.style.cursor = 'default';
            canvas.removeEventListener('mousedown', startCrop);
            canvas.removeEventListener('mousemove', moveCrop);
            canvas.removeEventListener('mouseup', endCrop);
        }
    }
}

function startCrop(e) {
    if (!cropMode) return;
    bakeFilters();
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    cropStart = {
        x: (e.clientX - rect.left) * scaleX,
        y: (e.clientY - rect.top) * scaleY
    };
    isCropping = true;
}

function moveCrop(e) {
    if (!isCropping || !cropMode) return;
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    cropEnd = {
        x: (e.clientX - rect.left) * scaleX,
        y: (e.clientY - rect.top) * scaleY
    };

    // Redraw with crop overlay
    ctx.putImageData(currentImage, 0, 0);
    ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    const x = Math.min(cropStart.x, cropEnd.x);
    const y = Math.min(cropStart.y, cropEnd.y);
    const w = Math.abs(cropEnd.x - cropStart.x);
    const h = Math.abs(cropEnd.y - cropStart.y);

    // Draw clear area
    ctx.clearRect(x, y, w, h);
    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = canvas.width;
    tempCanvas.height = canvas.height;
    const tempCtx = tempCanvas.getContext('2d');
    tempCtx.putImageData(currentImage, 0, 0);
    ctx.drawImage(tempCanvas, x, y, w, h, x, y, w, h);

    // Border
    ctx.strokeStyle = '#fff';
    ctx.lineWidth = 2;
    ctx.setLineDash([5, 5]);
    ctx.strokeRect(x, y, w, h);
    ctx.setLineDash([]);
}

function endCrop(e) {
    if (!isCropping || !cropMode) return;
    isCropping = false;

    if (!cropStart || !cropEnd) return;

    const x = Math.max(0, Math.round(Math.min(cropStart.x, cropEnd.x)));
    const y = Math.max(0, Math.round(Math.min(cropStart.y, cropEnd.y)));
    const w = Math.min(canvas.width - x, Math.round(Math.abs(cropEnd.x - cropStart.x)));
    const h = Math.min(canvas.height - y, Math.round(Math.abs(cropEnd.y - cropStart.y)));

    if (w < 10 || h < 10) {
        ctx.putImageData(currentImage, 0, 0);
        return;
    }

    if (confirm('Crop gambar ke area yang dipilih?')) {
        ctx.putImageData(currentImage, 0, 0);
        const cropData = ctx.getImageData(x, y, w, h);
        canvas.width = w;
        canvas.height = h;
        ctx.putImageData(cropData, 0, 0);
        currentImage = ctx.getImageData(0, 0, canvas.width, canvas.height);
        saveState();
        updateImageInfo();
    } else {
        ctx.putImageData(currentImage, 0, 0);
    }

    cropMode = false;
    document.getElementById('cropBtn').classList.remove('active');
    canvas.style.cursor = 'default';
    canvas.removeEventListener('mousedown', startCrop);
    canvas.removeEventListener('mousemove', moveCrop);
    canvas.removeEventListener('mouseup', endCrop);
}

// Resize
let aspectRatio = 1;

function openResizeModal() {
    document.getElementById('resizeWidth').value = canvas.width;
    document.getElementById('resizeHeight').value = canvas.height;
    aspectRatio = canvas.width / canvas.height;
    document.getElementById('resizeModal').classList.add('show');
}

function closeResizeModal() {
    document.getElementById('resizeModal').classList.remove('show');
}

document.getElementById('resizeWidth').addEventListener('input', () => {
    if (document.getElementById('lockAspect').checked) {
        const w = parseInt(document.getElementById('resizeWidth').value);
        document.getElementById('resizeHeight').value = Math.round(w / aspectRatio);
    }
});

document.getElementById('resizeHeight').addEventListener('input', () => {
    if (document.getElementById('lockAspect').checked) {
        const h = parseInt(document.getElementById('resizeHeight').value);
        document.getElementById('resizeWidth').value = Math.round(h * aspectRatio);
    }
});

function applyResize() {
    bakeFilters();
    const newW = parseInt(document.getElementById('resizeWidth').value);
    const newH = parseInt(document.getElementById('resizeHeight').value);

    if (newW < 1 || newH < 1 || newW > 10000 || newH > 10000) {
        alert('Ukuran tidak valid.');
        return;
    }

    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = newW;
    tempCanvas.height = newH;
    const tempCtx = tempCanvas.getContext('2d');
    tempCtx.drawImage(canvas, 0, 0, newW, newH);

    canvas.width = newW;
    canvas.height = newH;
    ctx.drawImage(tempCanvas, 0, 0);
    currentImage = ctx.getImageData(0, 0, canvas.width, canvas.height);
    saveState();
    updateImageInfo();
    closeResizeModal();
}

// Remove background (tolerance-based flood fill)
function removeBackground() {
    if (isRemovingBg) {
        isRemovingBg = false;
        document.getElementById('removeBgBtn').classList.remove('active');
        canvas.style.cursor = 'default';
        canvas.removeEventListener('click', removeBgClick);
        return;
    }

    bakeFilters();
    isRemovingBg = true;
    document.getElementById('removeBgBtn').classList.add('active');
    canvas.style.cursor = 'crosshair';
    canvas.addEventListener('click', removeBgClick);
    alert('Klik pada area background yang ingin dihapus.');
}

function removeBgClick(e) {
    if (!isRemovingBg) return;

    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    const x = Math.round((e.clientX - rect.left) * scaleX);
    const y = Math.round((e.clientY - rect.top) * scaleY);

    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const tolerance = 30;
    floodFillTransparent(imageData, x, y, tolerance);
    ctx.putImageData(imageData, 0, 0);
    currentImage = ctx.getImageData(0, 0, canvas.width, canvas.height);
    saveState();
}

function floodFillTransparent(imageData, startX, startY, tolerance) {
    const { width, height, data } = imageData;
    const startIdx = (startY * width + startX) * 4;
    const startR = data[startIdx];
    const startG = data[startIdx + 1];
    const startB = data[startIdx + 2];

    const visited = new Uint8Array(width * height);
    const stack = [[startX, startY]];

    while (stack.length > 0) {
        const [x, y] = stack.pop();
        if (x < 0 || x >= width || y < 0 || y >= height) continue;

        const pixelIdx = y * width + x;
        if (visited[pixelIdx]) continue;

        const idx = pixelIdx * 4;
        const dr = Math.abs(data[idx] - startR);
        const dg = Math.abs(data[idx + 1] - startG);
        const db = Math.abs(data[idx + 2] - startB);

        if (dr <= tolerance && dg <= tolerance && db <= tolerance) {
            visited[pixelIdx] = 1;
            data[idx + 3] = 0; // Make transparent
            stack.push([x + 1, y], [x - 1, y], [x, y + 1], [x, y - 1]);
        }
    }
}

// Download
function downloadImage() {
    // Bake filters before download
    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = canvas.width;
    tempCanvas.height = canvas.height;
    const tempCtx = tempCanvas.getContext('2d');
    tempCtx.filter = canvas.style.filter || 'none';
    tempCtx.drawImage(canvas, 0, 0);

    const link = document.createElement('a');
    link.download = 'edited-image.png';
    link.href = tempCanvas.toDataURL('image/png');
    link.click();
}
</script>
@endsection
