@extends('layouts.app')

@section('title', 'Buat Jadwal Bimbingan - SIMBA')

@section('content')
<style>
#klienDropdownMenu {
    scrollbar-width: thin;
    scrollbar-color: #CBD5E1 #F8FAFC;
}
#klienDropdownMenu::-webkit-scrollbar {
    width: 6px;
}
#klienDropdownMenu::-webkit-scrollbar-track {
    background: #F8FAFC;
    border-radius: 8px;
}
#klienDropdownMenu::-webkit-scrollbar-thumb {
    background: #CBD5E1;
    border-radius: 8px;
}
#klienDropdownMenu::-webkit-scrollbar-thumb:hover {
    background: #94A3B8;
}
</style>

<div class="container-fluid px-0">

    <!-- Page Header Row -->
    <div class="page-header-row mb-4">
        <div>
            <h2 class="page-title-main">Buat Jadwal Bimbingan</h2>
            <p class="page-subtitle-main">Jadwalkan sesi bimbingan baru untuk klien binaan Anda.</p>
        </div>
    </div>

    <!-- Form Card (Sesuai Buat Jadwal Figma) -->
    <div class="table-card-figma p-4 p-md-5 mb-5" style="border-radius: 16px; overflow: visible !important;">
        <form action="{{ route('pk.atur-jadwal.store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-12 col-md-6 position-relative" style="z-index: 1050;">
                    <label class="form-label-figma">Pilih Klien <span class="text-danger">*</span></label>
                    <div class="position-relative" id="comboboxKlienWrapper">
                        <div class="input-group">
                            <input type="text" 
                                   id="klien_input_search" 
                                   class="form-control form-control-figma" 
                                   placeholder="Pilih dari daftar atau ketik nama klien..." 
                                   autocomplete="off" 
                                   required
                                   style="padding-right: 3.5rem; border-radius: 8px !important;">
                            <div class="position-absolute end-0 top-50 translate-middle-y d-flex align-items-center pe-3" style="z-index: 5;">
                                <button type="button" id="btnClearKlien" class="btn btn-sm text-muted p-0 me-2" style="display: none; line-height: 1;" title="Hapus pilihan">
                                    <i class="bi bi-x-circle-fill" style="font-size: 0.95rem;"></i>
                                </button>
                                <button type="button" id="btnToggleKlienDropdown" class="btn btn-sm text-muted p-0" style="line-height: 1;" tabindex="-1" title="Buka daftar pilihan">
                                    <i class="bi bi-chevron-down" id="klienChevronIcon" style="transition: transform 0.2s ease; font-size: 0.95rem;"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Hidden input yang dikirim ke backend -->
                        <input type="hidden" name="id_klien" id="buat_id_klien" value="{{ old('id_klien', request('id_klien')) }}">

                        <!-- Dropdown Menu Pilihan Klien -->
                        <div id="klienDropdownMenu" 
                             class="border bg-white" 
                             style="display: none; position: absolute; top: calc(100% + 6px); left: 0; width: 100%; max-height: 280px; overflow-y: auto; z-index: 1060; border-radius: 10px; border-color: #CBD5E1 !important; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);">
                            
                            <div class="p-2 px-3 border-bottom bg-light text-muted" style="font-size: 0.76rem; font-weight: 500;">
                                <i class="bi bi-person-check me-1"></i> Pilih klien yang terdaftar di Data Klien
                            </div>

                            <div id="klienOptionsList">
                                @foreach($allKlien as $k)
                                    <div class="klien-option-item px-3 py-2 border-bottom" 
                                         data-id="{{ $k->id_klien }}" 
                                         data-nama="{{ $k->nama_klien }}" 
                                         data-noreg="{{ $k->no_register }}"
                                         data-jam="{{ $k->jam_mulai ? substr($k->jam_mulai, 0, 5) : '09:00' }}"
                                         style="cursor: pointer; transition: background 0.15s ease;">
                                        <div class="fw-semibold text-dark item-nama" style="font-size: 0.88rem;">{{ $k->nama_klien }}</div>
                                        <div class="text-muted small item-noreg" style="font-size: 0.76rem;">{{ $k->no_register }}</div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div id="klienNoItemsFound" class="p-3 text-muted text-center" style="display: none; font-size: 0.82rem;">
                                <i class="bi bi-exclamation-circle me-1"></i> Tidak ada klien yang cocok dalam Data Klien.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label-figma">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tgl_bimbingan" class="form-control form-control-figma" value="{{ old('tgl_bimbingan') }}" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label-figma">Waktu <span class="text-danger">*</span></label>
                    <input type="time" name="jam_mulai" id="buat_jam_mulai" class="form-control form-control-figma" value="{{ old('jam_mulai', '09:00') }}" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label-figma">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control form-control-figma" placeholder="Catatan bimbingan opsional" value="{{ old('keterangan') }}">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-4 pt-3">
                <a href="{{ route('pk.jadwal.index') }}" class="btn btn-figma-cancel" style="min-width: 140px; height: 42px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">Batal</a>
                <button type="submit" class="btn btn-figma-save" style="min-width: 140px; height: 42px; display: inline-flex; align-items: center; justify-content: center;">Simpan</button>
            </div>
        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputSearch = document.getElementById('klien_input_search');
    const hiddenId = document.getElementById('buat_id_klien');
    const dropdownMenu = document.getElementById('klienDropdownMenu');
    const chevronIcon = document.getElementById('klienChevronIcon');
    const btnToggle = document.getElementById('btnToggleKlienDropdown');
    const btnClear = document.getElementById('btnClearKlien');
    const noItems = document.getElementById('klienNoItemsFound');
    const optionItems = document.querySelectorAll('.klien-option-item');
    const jamMulaiInput = document.getElementById('buat_jam_mulai');

    function openDropdown() {
        dropdownMenu.style.display = 'block';
        chevronIcon.style.transform = 'rotate(180deg)';
        dropdownMenu.scrollTop = 0;
    }

    function closeDropdown() {
        dropdownMenu.style.display = 'none';
        chevronIcon.style.transform = 'rotate(0deg)';
    }

    function toggleDropdown() {
        if (dropdownMenu.style.display === 'block') {
            closeDropdown();
        } else {
            openDropdown();
            inputSearch.focus();
        }
    }

    function filterClients() {
        const query = inputSearch.value.trim().toLowerCase();
        let matchesCount = 0;

        optionItems.forEach(item => {
            const nama = (item.dataset.nama || '').toLowerCase();
            const noreg = (item.dataset.noreg || '').toLowerCase();

            if (query === '' || nama.includes(query) || noreg.includes(query)) {
                item.style.display = 'block';
                matchesCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (query.length > 0) {
            btnClear.style.display = 'inline-block';
            hiddenId.value = inputSearch.value.trim();
        } else {
            btnClear.style.display = 'none';
            hiddenId.value = '';
        }

        if (matchesCount === 0 && query.length > 0) {
            noItems.style.display = 'block';
        } else {
            noItems.style.display = 'none';
        }
        dropdownMenu.scrollTop = 0;
    }

    function selectClientItem(item) {
        const nama = item.dataset.nama;
        const noreg = item.dataset.noreg;
        const id = item.dataset.id;
        const jam = item.dataset.jam;

        inputSearch.value = `${nama} (${noreg})`;
        hiddenId.value = id;

        if (jam && jamMulaiInput) {
            jamMulaiInput.value = jam;
        }

        btnClear.style.display = 'inline-block';
        closeDropdown();
    }

    inputSearch.addEventListener('focus', function () {
        filterClients();
        openDropdown();
    });

    inputSearch.addEventListener('input', function () {
        filterClients();
        openDropdown();
    });

    inputSearch.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            const visibleItems = Array.from(optionItems).filter(item => item.style.display !== 'none');
            if (visibleItems.length === 1) {
                e.preventDefault();
                selectClientItem(visibleItems[0]);
            }
        }
    });

    btnToggle.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        toggleDropdown();
    });

    btnClear.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        inputSearch.value = '';
        hiddenId.value = '';
        btnClear.style.display = 'none';
        filterClients();
        inputSearch.focus();
    });

    optionItems.forEach(item => {
        item.addEventListener('mouseenter', function () {
            this.style.backgroundColor = '#F1F5F9';
        });
        item.addEventListener('mouseleave', function () {
            this.style.backgroundColor = 'transparent';
        });
        item.addEventListener('click', function (e) {
            e.stopPropagation();
            selectClientItem(this);
        });
    });

    document.addEventListener('click', function (e) {
        const wrapper = document.getElementById('comboboxKlienWrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            closeDropdown();
            if (inputSearch.value.trim() !== '' && !hiddenId.value) {
                hiddenId.value = inputSearch.value.trim();
            }
        }
    });

    // Inisialisasi awal jika ada nilai terpilih
    const initId = hiddenId.value;
    if (initId) {
        optionItems.forEach(item => {
            if (item.dataset.id == initId) {
                selectClientItem(item);
            }
        });
    }

    // Pastikan hiddenId terisi saat submit form
    const form = inputSearch.closest('form');
    if (form) {
        form.addEventListener('submit', function () {
            if (!hiddenId.value && inputSearch.value.trim()) {
                hiddenId.value = inputSearch.value.trim();
            }
        });
    }
});
</script>
@endsection
