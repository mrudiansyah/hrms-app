<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Data Kandidat</title>
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5">
    <h1 class="h3">Lengkapi Data Kandidat</h1>
    <p>
        <?php if($candidate->verified_at): ?>
            Email terverifikasi: <strong><?php echo e($candidate->email_address); ?></strong>
        <?php else: ?>
            Email tidak diverifikasi (opsi skip dari HR): <strong><?php echo e($candidate->email_address); ?></strong>
        <?php endif; ?>
    </p>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('candidate.profile.save')); ?>">
        <?php echo csrf_field(); ?>
        <section class="card mb-4">
            <div class="card-header">Data Pribadi</div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Tempat lahir</label>
                        <input name="tempat_lahir" class="form-control" value="<?php echo e(old('tempat_lahir')); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Tanggal lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="<?php echo e(old('tanggal_lahir')); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Agama</label>
                        <select name="agama" class="form-control" required>
                            <option value="">Pilih</option>
                            <?php $__currentLoopData = $religions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $religion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($religion); ?>" <?php if(old('agama') === $religion): echo 'selected'; endif; ?>><?php echo e($religion); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Golongan darah</label>
                        <select name="golongan_darah" class="form-control" required>
                            <option value="">Pilih</option>
                            <?php $__currentLoopData = ['A', 'B', 'AB', 'O']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bloodType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($bloodType); ?>" <?php if(old('golongan_darah') === $bloodType): echo 'selected'; endif; ?>><?php echo e($bloodType); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Nama ibu kandung</label>
                        <input name="ibu_kandung" class="form-control" value="<?php echo e(old('ibu_kandung')); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Nomor KTP (16 angka)</label>
                        <input name="nomor_ktp" class="form-control" inputmode="numeric" maxlength="16" value="<?php echo e(old('nomor_ktp')); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Nomor KK (16 angka)</label>
                        <input name="nomor_kk" class="form-control" inputmode="numeric" maxlength="16" value="<?php echo e(old('nomor_kk')); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>NPWP</label>
                        <input name="nomor_npwp" class="form-control" value="<?php echo e(old('nomor_npwp')); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Status pajak</label>
                        <select name="kode_status" class="form-control" required>
                            <option value="">Pilih</option>
                            <?php $__currentLoopData = $taxStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taxStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($taxStatus); ?>" <?php if(old('kode_status') === $taxStatus): echo 'selected'; endif; ?>><?php echo e($taxStatus); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Nomor telepon</label>
                        <input name="nomor_telepon" class="form-control" value="<?php echo e(old('nomor_telepon')); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Nomor BPJS Kesehatan</label>
                        <input name="nomor_bpjs_kes" class="form-control" value="<?php echo e(old('nomor_bpjs_kes')); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Nomor BPJS Ketenagakerjaan</label>
                        <input name="nomor_bpjs_ket" class="form-control" value="<?php echo e(old('nomor_bpjs_ket')); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Nomor rekening</label>
                        <input name="nomor_rekening" class="form-control" value="<?php echo e(old('nomor_rekening')); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Nama bank</label>
                        <input name="nama_bank" class="form-control" value="<?php echo e(old('nama_bank')); ?>">
                    </div>
                </div>
            </div>
        </section>

        <?php $__currentLoopData = ['ktp' => 'Alamat sesuai KTP', 'domicile' => 'Alamat domisili']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addressKey => $addressTitle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <section class="card mb-4 region-group">
                <div class="card-header"><?php echo e($addressTitle); ?></div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Provinsi</label>
                            <select name="addresses[<?php echo e($addressKey); ?>][provinsi]" class="form-control region-select" data-region-level="province" required>
                                <option value="">Pilih provinsi</option>
                                <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($province->kode); ?>" <?php if(old("addresses.$addressKey.provinsi") === $province->kode): echo 'selected'; endif; ?>><?php echo e($province->nama); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <?php $__currentLoopData = ['regency' => ['kabupaten', 'Kabupaten/Kota', 'Pilih kabupaten/kota'], 'district' => ['kecamatan', 'Kecamatan', 'Pilih kecamatan'], 'village' => ['kelurahan', 'Kelurahan/Desa', 'Pilih kelurahan/desa']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level => $regionField): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-group col-md-6" data-region-container="<?php echo e($level); ?>" hidden>
                                <label><?php echo e($regionField[1]); ?></label>
                                <select
                                    name="addresses[<?php echo e($addressKey); ?>][<?php echo e($regionField[0]); ?>]"
                                    class="form-control region-select"
                                    data-region-level="<?php echo e($level); ?>"
                                    data-selected="<?php echo e(old("addresses.$addressKey.".$regionField[0])); ?>"
                                    disabled
                                    required
                                >
                                    <option value=""><?php echo e($regionField[2]); ?></option>
                                </select>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <div class="alert alert-danger region-error col-12" role="alert" hidden></div>
                        <div class="form-group col-12">
                            <label>Detail alamat</label>
                            <textarea name="addresses[<?php echo e($addressKey); ?>][detail]" class="form-control" required><?php echo e(old("addresses.$addressKey.detail")); ?></textarea>
                        </div>
                        <div class="form-group col-12">
                            <label>Link/pin lokasi peta (opsional)</label>
                            <input name="addresses[<?php echo e($addressKey); ?>][map_address]" class="form-control" value="<?php echo e(old("addresses.$addressKey.map_address")); ?>">
                        </div>
                    </div>
                </div>
            </section>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <section class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Riwayat Pendidikan (minimal satu)</span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-education">Tambah pendidikan</button>
            </div>
            <div class="card-body" id="education-list">
                <?php $__currentLoopData = old('educations', [['level_education' => '', 'institute' => '', 'prodi' => '', 'year' => '', 'graduate_year' => '', 'remark' => '', 'is_top' => '1']]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $education): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded p-3 mb-3 education-row">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Jenjang</label>
                                <select name="educations[<?php echo e($index); ?>][level_education]" class="form-control" required>
                                    <option value="">Pilih</option>
                                    <?php $__currentLoopData = $educationLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($level); ?>" <?php if(($education['level_education'] ?? '') === $level): echo 'selected'; endif; ?>><?php echo e($level); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group col-md-8">
                                <label>Institusi</label>
                                <input name="educations[<?php echo e($index); ?>][institute]" class="form-control" value="<?php echo e($education['institute'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Program studi</label>
                                <input name="educations[<?php echo e($index); ?>][prodi]" class="form-control" value="<?php echo e($education['prodi'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Tahun mulai</label>
                                <input type="number" name="educations[<?php echo e($index); ?>][year]" class="form-control" value="<?php echo e($education['year'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Tahun lulus</label>
                                <input type="number" name="educations[<?php echo e($index); ?>][graduate_year]" class="form-control" value="<?php echo e($education['graduate_year'] ?? ''); ?>">
                            </div>
                            <div class="form-group col-12">
                                <label>Keterangan (opsional)</label>
                                <input name="educations[<?php echo e($index); ?>][remark]" class="form-control" value="<?php echo e($education['remark'] ?? ''); ?>">
                            </div>
                            <div class="form-group col-12 form-check ml-3">
                                <input type="checkbox" name="educations[<?php echo e($index); ?>][is_top]" value="1" class="form-check-input" <?php if(!empty($education['is_top'])): echo 'checked'; endif; ?>>
                                <label class="form-check-label">Pendidikan tertinggi</label>
                            </div>
                            <div class="col-12"><button type="button" class="btn btn-sm btn-outline-danger remove-row">Hapus baris</button></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <section class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Kontak Darurat (minimal satu)</span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-emergency">Tambah kontak</button>
            </div>
            <div class="card-body" id="emergency-list">
                <?php $__currentLoopData = old('emergency_contacts', [['nama_keluarga' => '', 'hubungan' => '', 'nomor_kontak' => '', 'provinsi_kontak' => '', 'kabupaten_kontak' => '', 'kecamatan_kontak' => '', 'kelurahan_kontak' => '', 'detail_kontak' => '']]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded p-3 mb-3 emergency-row region-group">
                        <div class="form-row">
                            <?php $__currentLoopData = ['nama_keluarga' => 'Nama kontak', 'hubungan' => 'Hubungan', 'nomor_kontak' => 'Nomor telepon']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="form-group col-md-6">
                                    <label><?php echo e($label); ?></label>
                                    <input name="emergency_contacts[<?php echo e($index); ?>][<?php echo e($field); ?>]" class="form-control" value="<?php echo e($contact[$field] ?? ''); ?>" required>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-group col-md-6">
                                <label>Provinsi</label>
                                <select name="emergency_contacts[<?php echo e($index); ?>][provinsi_kontak]" class="form-control region-select" data-region-level="province" required>
                                    <option value="">Pilih provinsi</option>
                                    <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($province->kode); ?>" <?php if(($contact['provinsi_kontak'] ?? '') === $province->kode): echo 'selected'; endif; ?>><?php echo e($province->nama); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php $__currentLoopData = ['regency' => ['kabupaten_kontak', 'Kabupaten/Kota', 'Pilih kabupaten/kota'], 'district' => ['kecamatan_kontak', 'Kecamatan', 'Pilih kecamatan'], 'village' => ['kelurahan_kontak', 'Kelurahan/Desa', 'Pilih kelurahan/desa']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level => $regionField): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="form-group col-md-6" data-region-container="<?php echo e($level); ?>" hidden>
                                    <label><?php echo e($regionField[1]); ?></label>
                                    <select
                                        name="emergency_contacts[<?php echo e($index); ?>][<?php echo e($regionField[0]); ?>]"
                                        class="form-control region-select"
                                        data-region-level="<?php echo e($level); ?>"
                                        data-selected="<?php echo e($contact[$regionField[0]] ?? ''); ?>"
                                        disabled
                                        required
                                    >
                                        <option value=""><?php echo e($regionField[2]); ?></option>
                                    </select>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <div class="alert alert-danger region-error col-12" role="alert" hidden></div>
                            <div class="form-group col-12">
                                <label>Detail alamat</label>
                                <textarea name="emergency_contacts[<?php echo e($index); ?>][detail_kontak]" class="form-control" required><?php echo e($contact['detail_kontak'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-12"><button type="button" class="btn btn-sm btn-outline-danger remove-row">Hapus baris</button></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <section class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Data Keluarga (opsional)</span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-family">Tambah keluarga</button>
            </div>
            <div class="card-body" id="family-list">
                <?php $__currentLoopData = old('families', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $family): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded p-3 mb-3 family-row">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Nama keluarga</label>
                                <input name="families[<?php echo e($index); ?>][nama_keluarga]" class="form-control" value="<?php echo e($family['nama_keluarga'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Hubungan</label>
                                <select name="families[<?php echo e($index); ?>][hubungan]" class="form-control" required>
                                    <option value="">Pilih</option>
                                    <?php $__currentLoopData = $familyRelationships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $relationship): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($relationship); ?>" <?php if(($family['hubungan'] ?? '') === $relationship): echo 'selected'; endif; ?>><?php echo e($relationship); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Tanggal lahir</label>
                                <input type="date" name="families[<?php echo e($index); ?>][tanggal_lahir]" class="form-control" value="<?php echo e($family['tanggal_lahir'] ?? ''); ?>" required>
                            </div>
                            <div class="col-12"><button type="button" class="btn btn-sm btn-outline-danger remove-row">Hapus baris</button></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <button type="submit" class="btn btn-primary btn-lg">Simpan data kandidat</button>
    </form>
</main>

<script>
(() => {
    const educationLevels = <?php echo json_encode($educationLevels, 15, 512) ?>;
    const familyRelationships = <?php echo json_encode($familyRelationships, 15, 512) ?>;
    const provinces = <?php echo json_encode($provinces, 15, 512) ?>;
    const regionEndpoint = <?php echo json_encode(url('/candidate/regions'), 15, 512) ?>;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const nextRegionLevel = {province: 'regency', regency: 'district', district: 'village'};
    const regionDescendants = {
        province: ['regency', 'district', 'village'],
        regency: ['district', 'village'],
        district: ['village']
    };
    const regionPlaceholders = {
        regency: 'Pilih kabupaten/kota',
        district: 'Pilih kecamatan',
        village: 'Pilih kelurahan/desa'
    };
    const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[char]));
    const getRegionSelect = (group, level) => group.querySelector(`[data-region-level="${level}"]`);
    const setRegionError = (group, message) => {
        const error = group.querySelector('.region-error');
        error.textContent = message;
        error.hidden = !message;
    };
    const clearRegionDescendants = (group, level) => {
        (regionDescendants[level] || []).forEach((descendant) => {
            const select = getRegionSelect(group, descendant);
            const container = group.querySelector(`[data-region-container="${descendant}"]`);
            select.innerHTML = `<option value="">${regionPlaceholders[descendant]}</option>`;
            select.disabled = true;
            container.hidden = true;
        });
    };
    const loadRegionChildren = async (group, parentLevel, parentCode, selectedCode) => {
        const childLevel = nextRegionLevel[parentLevel];
        if (!childLevel || !parentCode) return false;

        const child = getRegionSelect(group, childLevel);
        const container = group.querySelector(`[data-region-container="${childLevel}"]`);
        const response = await fetch(`${regionEndpoint}/${childLevel}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({id: parentCode})
        });
        if (!response.ok) throw new Error('Wilayah gagal dimuat.');

        const regions = await response.json();
        child.innerHTML = `<option value="">${regionPlaceholders[childLevel]}</option>`;
        regions.forEach((region) => child.add(new Option(region.name, region.code)));
        child.disabled = regions.length === 0;
        container.hidden = regions.length === 0;
        if (selectedCode) child.value = selectedCode;

        return regions.length > 0 && (!selectedCode || child.value === selectedCode);
    };
    const restoreRegionGroup = async (group) => {
        try {
            const province = getRegionSelect(group, 'province');
            if (!province.value) return;

            const regency = getRegionSelect(group, 'regency');
            const district = getRegionSelect(group, 'district');
            const village = getRegionSelect(group, 'village');
            if (!await loadRegionChildren(group, 'province', province.value, regency.dataset.selected)) return;
            if (!regency.value || !await loadRegionChildren(group, 'regency', regency.value, district.dataset.selected)) return;
            if (district.value) await loadRegionChildren(group, 'district', district.value, village.dataset.selected);
        } catch (error) {
            setRegionError(group, 'Data wilayah gagal dimuat. Silakan pilih ulang.');
        }
    };
    document.addEventListener('change', async (event) => {
        const select = event.target.closest('.region-select');
        if (!select) return;

        const group = select.closest('.region-group');
        const level = select.dataset.regionLevel;
        clearRegionDescendants(group, level);
        setRegionError(group, '');
        if (!nextRegionLevel[level] || !select.value) return;

        try {
            const loaded = await loadRegionChildren(group, level, select.value);
            if (!loaded) setRegionError(group, 'Pilihan wilayah tidak tersedia untuk lokasi tersebut.');
        } catch (error) {
            setRegionError(group, 'Data wilayah gagal dimuat. Silakan coba lagi.');
        }
    });
    const nextIndex = (container, selector, field) => {
        let next = 0;
        container.querySelectorAll(selector).forEach((row) => {
            row.querySelectorAll(`[name^="${field}["]`).forEach((input) => {
                const match = input.name.match(new RegExp(`^${field}\\[(\\d+)\\]`));
                if (match) next = Math.max(next, Number(match[1]) + 1);
            });
        });
        return next;
    };
    const wireRemoveButtons = (container) => {
        container.querySelectorAll('.remove-row').forEach((button) => {
            button.onclick = () => button.closest('.education-row, .emergency-row, .family-row').remove();
        });
    };
    const educationList = document.getElementById('education-list');
    const emergencyList = document.getElementById('emergency-list');
    const familyList = document.getElementById('family-list');
    const provinceOptions = () => '<option value="">Pilih provinsi</option>' + provinces
        .map((province) => `<option value="${escapeHtml(province.kode)}">${escapeHtml(province.nama)}</option>`)
        .join('');
    const emergencyRegionFields = (index) => {
        const fields = [
            ['provinsi_kontak', 'province', 'Provinsi', 'Pilih provinsi'],
            ['kabupaten_kontak', 'regency', 'Kabupaten/Kota', 'Pilih kabupaten/kota'],
            ['kecamatan_kontak', 'district', 'Kecamatan', 'Pilih kecamatan'],
            ['kelurahan_kontak', 'village', 'Kelurahan/Desa', 'Pilih kelurahan/desa']
        ];
        return fields.map(([field, level, label, placeholder]) => {
            const hidden = level === 'province' ? '' : ' hidden';
            const disabled = level === 'province' ? '' : ' disabled';
            const options = level === 'province'
                ? provinceOptions()
                : `<option value="">${placeholder}</option>`;
            return `<div class="form-group col-md-6" data-region-container="${level}"${hidden}>
                <label>${label}</label>
                <select name="emergency_contacts[${index}][${field}]" class="form-control region-select" data-region-level="${level}"${disabled} required>${options}</select>
            </div>`;
        }).join('');
    };
    document.getElementById('add-education').addEventListener('click', () => {
        const index = nextIndex(educationList, '.education-row', 'educations');
        const options = educationLevels.map((level) => `<option value="${escapeHtml(level)}">${escapeHtml(level)}</option>`).join('');
        educationList.insertAdjacentHTML('beforeend', `<div class="border rounded p-3 mb-3 education-row"><div class="form-row">
            <div class="form-group col-md-4"><label>Jenjang</label><select name="educations[${index}][level_education]" class="form-control" required><option value="">Pilih</option>${options}</select></div>
            <div class="form-group col-md-8"><label>Institusi</label><input name="educations[${index}][institute]" class="form-control" required></div>
            <div class="form-group col-md-6"><label>Program studi</label><input name="educations[${index}][prodi]" class="form-control" required></div>
            <div class="form-group col-md-3"><label>Tahun mulai</label><input type="number" name="educations[${index}][year]" class="form-control" required></div>
            <div class="form-group col-md-3"><label>Tahun lulus</label><input type="number" name="educations[${index}][graduate_year]" class="form-control"></div>
            <div class="form-group col-12"><label>Keterangan</label><input name="educations[${index}][remark]" class="form-control"></div>
            <div class="form-group col-12 form-check ml-3"><input type="checkbox" name="educations[${index}][is_top]" value="1" class="form-check-input"><label class="form-check-label">Pendidikan tertinggi</label></div>
            <div class="col-12"><button type="button" class="btn btn-sm btn-outline-danger remove-row">Hapus baris</button></div>
        </div></div>`);
        wireRemoveButtons(educationList);
    });
    document.getElementById('add-emergency').addEventListener('click', () => {
        const index = nextIndex(emergencyList, '.emergency-row', 'emergency_contacts');
        const contactFields = {
            nama_keluarga: 'Nama kontak', hubungan: 'Hubungan', nomor_kontak: 'Nomor telepon'
        };
        const inputs = Object.entries(contactFields).map(([field, label]) => `<div class="form-group col-md-6"><label>${label}</label><input name="emergency_contacts[${index}][${field}]" class="form-control" required></div>`).join('');
        emergencyList.insertAdjacentHTML('beforeend', `<div class="border rounded p-3 mb-3 emergency-row region-group"><div class="form-row">
            ${inputs}${emergencyRegionFields(index)}
            <div class="alert alert-danger region-error col-12" role="alert" hidden></div>
            <div class="form-group col-12"><label>Detail alamat</label><textarea name="emergency_contacts[${index}][detail_kontak]" class="form-control" required></textarea></div>
            <div class="col-12"><button type="button" class="btn btn-sm btn-outline-danger remove-row">Hapus baris</button></div>
        </div></div>`);
        wireRemoveButtons(emergencyList);
    });
    document.getElementById('add-family').addEventListener('click', () => {
        const index = nextIndex(familyList, '.family-row', 'families');
        const options = familyRelationships.map((relationship) => `<option value="${escapeHtml(relationship)}">${escapeHtml(relationship)}</option>`).join('');
        familyList.insertAdjacentHTML('beforeend', `<div class="border rounded p-3 mb-3 family-row"><div class="form-row">
            <div class="form-group col-md-4"><label>Nama keluarga</label><input name="families[${index}][nama_keluarga]" class="form-control" required></div>
            <div class="form-group col-md-4"><label>Hubungan</label><select name="families[${index}][hubungan]" class="form-control" required><option value="">Pilih</option>${options}</select></div>
            <div class="form-group col-md-4"><label>Tanggal lahir</label><input type="date" name="families[${index}][tanggal_lahir]" class="form-control" required></div>
            <div class="col-12"><button type="button" class="btn btn-sm btn-outline-danger remove-row">Hapus baris</button></div>
        </div></div>`);
        wireRemoveButtons(familyList);
    });
    wireRemoveButtons(document);
    document.querySelectorAll('.region-group').forEach(restoreRegionGroup);
})();
</script>
</body>
</html>
<?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/candidate/profile.blade.php ENDPATH**/ ?>