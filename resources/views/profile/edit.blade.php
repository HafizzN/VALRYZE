@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('breadcrumb', 'Pengaturan › Profil Saya')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Alert Status --}}
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>Informasi profil Anda berhasil diperbarui.</span>
        </div>
    @endif
    @if (session('status') === 'password-updated')
        <div class="alert alert-success">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>Kata sandi Anda berhasil diperbarui.</span>
        </div>
    @endif

    {{-- Update Profile Information --}}
    <div class="card">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border-dim);">
            <div style="width:38px;height:38px;border-radius:10px;background:var(--em-ghost);border:1px solid var(--em-border);display:flex;align-items:center;justify-content:center;">
                <svg style="width:18px;height:18px;color:var(--em);" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <h3 style="font-size:0.95rem;font-weight:700;color:var(--t1);">Informasi Profil</h3>
                <p style="font-size:0.73rem;color:var(--t4);margin-top:0.1rem;">Perbarui nama dan alamat email akun Anda.</p>
            </div>
        </div>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-4" enctype="multipart/form-data" id="profile-form">
            @csrf
            @method('patch')
            <input type="hidden" name="cropped_photo" id="cropped_photo">

            {{-- Profile Photo Preview --}}
            <div style="display:flex;align-items:center;gap:1.5rem;margin-bottom:1.75rem;padding:1.25rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:16px;">
                {{-- Avatar circle --}}
                <div style="position:relative;flex-shrink:0;">
                    @if($user->photo)
                        <img id="profile-preview-img" src="{{ $user->photo_url }}" alt="{{ $user->name }}"
                             style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--em);box-shadow:0 0 0 4px var(--em-ghost);">
                    @else
                        <div id="profile-preview-initials"
                             style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#0C2E4A,#1E4B70);color:var(--em);display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:800;border:3px solid var(--em);box-shadow:0 0 0 4px var(--em-ghost);text-transform:uppercase;">
                            {{ $user->initials }}
                        </div>
                        <img id="profile-preview-img"
                             style="display:none;width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--em);box-shadow:0 0 0 4px var(--em-ghost);">
                    @endif
                    {{-- Online dot --}}
                    <div style="position:absolute;bottom:3px;right:3px;width:14px;height:14px;border-radius:50%;background:var(--em);border:2px solid var(--bg-elevated);box-shadow:0 0 6px var(--em-glow);"></div>
                </div>
                {{-- Info + Upload trigger --}}
                <div style="flex:1;">
                    <div style="font-size:1rem;font-weight:800;color:var(--t1);">{{ $user->name }}</div>
                    <div style="font-size:0.72rem;color:var(--t4);margin-top:0.15rem;">{{ $user->email }}</div>
                    <div style="margin-top:0.85rem;">
                        <label for="photo"
                               style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.45rem 1rem;border-radius:10px;background:var(--em-ghost);border:1px solid var(--em-border);color:var(--em);font-size:0.78rem;font-weight:700;cursor:pointer;transition:all 0.2s;"
                               onmouseover="this.style.background='var(--em)';this.style.color='#fff';"
                               onmouseout="this.style.background='var(--em-ghost)';this.style.color='var(--em)';">
                            <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Ganti Foto
                        </label>
                        <div style="font-size:0.67rem;color:var(--t5);margin-top:0.4rem;">JPG, PNG · Maks 2 MB · Akan dipotong jadi lingkaran</div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autocomplete="name">
                @if($errors->has('name'))
                    <span class="form-error">{{ $errors->first('name') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username">
                @if($errors->has('email'))
                    <span class="form-error">{{ $errors->first('email') }}</span>
                @endif
            </div>

            {{-- Hidden file input (triggered by the button in photo section above) --}}
            <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/jpg" style="display:none;">
            @if($errors->has('photo'))
                <span class="form-error">{{ $errors->first('photo') }}</span>
            @endif

            <div style="display: flex; align-items: center; gap: 1rem; padding-top: 0.5rem;">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    {{-- Update Password --}}
    <div class="card">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border-dim);">
            <div style="width:38px;height:38px;border-radius:10px;background:rgba(124,58,237,0.1);border:1px solid rgba(124,58,237,0.2);display:flex;align-items:center;justify-content:center;">
                <svg style="width:18px;height:18px;color:#A78BFA;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <h3 style="font-size:0.95rem;font-weight:700;color:var(--t1);">Perbarui Kata Sandi</h3>
                <p style="font-size:0.73rem;color:var(--t4);margin-top:0.1rem;">Gunakan kata sandi kuat dan unik untuk menjaga keamanan akun.</p>
            </div>
        </div>

        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div class="form-group">
                <label for="update_password_current_password" class="form-label">Kata Sandi Saat Ini</label>
                <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" required>
                @if($errors->updatePassword->has('current_password'))
                    <span class="form-error">{{ $errors->updatePassword->first('current_password') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="update_password_password" class="form-label">Kata Sandi Baru</label>
                <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password" required>
                @if($errors->updatePassword->has('password'))
                    <span class="form-error">{{ $errors->updatePassword->first('password') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="update_password_password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" required>
                @if($errors->updatePassword->has('password_confirmation'))
                    <span class="form-error">{{ $errors->updatePassword->first('password_confirmation') }}</span>
                @endif
            </div>

            <div style="display: flex; align-items: center; gap: 1rem; padding-top: 0.5rem;">
                <button type="submit" class="btn btn-primary">Perbarui Kata Sandi</button>
            </div>
        </form>
    </div>

    {{-- Delete User Account --}}
    <div class="card" style="border-color: rgba(239,68,68,0.25); background: linear-gradient(145deg, #161b27, rgba(239,68,68,0.02));" x-data="{ openModal: false }">
        <div style="margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(239,68,68,0.15);">
            <h3 style="font-size: 1rem; font-weight: 600; color: #f87171;">Hapus Akun</h3>
            <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Setelah akun Anda dihapus, seluruh data dan informasi di dalamnya akan dihapus secara permanen.</p>
        </div>

        <div style="margin-bottom: 1rem;">
            <p style="font-size: 0.82rem; color: #cbd5e1; line-height: 1.5;">
                Sebelum menghapus akun Anda, harap unduh data atau informasi penting apa pun yang ingin Anda simpan dari sistem ini.
            </p>
        </div>

        <button @click="openModal = true" type="button" class="btn btn-danger">Hapus Akun Saya</button>

        {{-- Delete Account Modal --}}
        <div x-show="openModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 50; padding: 1rem;" x-cloak>
            <div @click.away="openModal = false" class="card" style="max-width: 500px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: rgba(255,255,255,0.08);">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #e2e8f0; margin-bottom: 0.75rem;">Apakah Anda yakin ingin menghapus akun?</h3>
                <p style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 1.5rem; line-height: 1.5;">
                    Setelah akun Anda dihapus, seluruh data akan dihapus secara permanen. Harap masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun secara permanen.
                </p>

                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="form-group">
                        <label for="password" class="form-label sr-only">Kata Sandi</label>
                        <input id="password" name="password" type="password" class="form-control" placeholder="Kata Sandi Anda" required>
                        @if($errors->userDeletion->has('password'))
                            <span class="form-error">{{ $errors->userDeletion->first('password') }}</span>
                        @endif
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                        <button type="button" @click="openModal = false" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Crop Modal --}}
    <div id="crop-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(4px); align-items: center; justify-content: center; z-index: 9999; padding: 1rem;">
        <div class="card" style="max-width: 500px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: rgba(255,255,255,0.08); display: flex; flex-direction: column; max-height: 90vh;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #e2e8f0; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                Sesuaikan & Potong Foto
            </h3>
            <div style="flex: 1; min-height: 0; background: #0d1117; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.06);">
                <img id="crop-image" style="max-width: 100%; max-height: 50vh; display: block;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" id="crop-cancel-btn" class="btn btn-secondary">Batal</button>
                <button type="button" id="crop-apply-btn" class="btn btn-primary">Potong & Terapkan</button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById('photo');
    const cropModal = document.getElementById('crop-modal');
    const cropImage = document.getElementById('crop-image');
    const cropCancelBtn = document.getElementById('crop-cancel-btn');
    const cropApplyBtn = document.getElementById('crop-apply-btn');
    const croppedPhotoInput = document.getElementById('cropped_photo');
    const previewImg = document.getElementById('profile-preview-img');
    const previewInitials = document.getElementById('profile-preview-initials');

    let cropper = null;

    photoInput.addEventListener('change', function (e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const file = files[0];

            // Check if file is image
            if (!file.type.startsWith('image/')) {
                alert('Silakan pilih file gambar yang valid (JPG/PNG).');
                photoInput.value = '';
                return;
            }

            // Check if file size is too large (max 5MB for original file before crop)
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Batas maksimal adalah 5MB.');
                photoInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                cropImage.src = event.target.result;
                cropModal.style.display = 'flex';

                // Initialize Cropper.js
                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false
                });
            };
            reader.readAsDataURL(file);
        }
    });

    cropCancelBtn.addEventListener('click', function () {
        closeCropModal();
        photoInput.value = ''; // Reset file input
    });

    cropApplyBtn.addEventListener('click', function () {
        if (!cropper) return;

        // Get 400x400 canvas for high quality avatar
        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400
        });

        // Compress image at 0.85 quality
        const base64Data = canvas.toDataURL('image/jpeg', 0.85);

        // Set hidden input value
        croppedPhotoInput.value = base64Data;

        // Update page previews
        if (previewImg) {
            previewImg.src = base64Data;
            previewImg.style.display = 'block';
        }
        if (previewInitials) {
            previewInitials.style.display = 'none';
        }

        closeCropModal();
    });

    function closeCropModal() {
        cropModal.style.display = 'none';
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }

    // Intercept form submissions to check for CSRF token expiration (HTTP 419)
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (form.dataset.validated === 'true') {
                return;
            }
            
            e.preventDefault();
            
            const csrfToken = form.querySelector('input[name="_token"]')?.value;
            if (!csrfToken) {
                form.dataset.validated = 'true';
                form.submit();
                return;
            }
            
            fetch('/csrf-ping', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            }).then(response => {
                if (response.status === 419) {
                    window.location.reload();
                } else {
                    form.dataset.validated = 'true';
                    form.submit();
                }
            }).catch(() => {
                form.dataset.validated = 'true';
                form.submit();
            });
        });
    });
});
</script>
@endpush
