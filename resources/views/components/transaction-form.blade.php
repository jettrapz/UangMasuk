<div class="form-grid">
    <div class="field"><label for="nama">Nama</label><input class="input" id="nama" name="nama" required
            value="{{ old('nama', $value ?? null) }}"></div>
    <div class="field"><label for="tanggal_main">Tanggal Main</label><input type="date" class="input" id="tanggal_main"
            name="tanggal_main" required value="{{ old('tanggal_main', $dateValue ?? null) }}">
    </div>
    <div class="field">
        <label class="field-label">Jenis Pembayaran</label>
        <div class="radio-group">
            @php
                $currentType = old('jenis_transfer', $selectedType ?? 'Transfer');
            @endphp

            <!-- Option: QRIS -->
            <label class="radio-card">
                <input type="radio" name="jenis_transfer" value="Qris" @checked($currentType === 'Qris')>
                <div class="radio-content">
                    <span class="radio-icon">📱</span>
                    <span class="radio-title">QRIS</span>
                </div>
            </label>

            <!-- Option: Transfer -->
            <label class="radio-card">
                <input type="radio" name="jenis_transfer" value="Transfer" @checked($currentType === 'Transfer')>
                <div class="radio-content">
                    <span class="radio-icon">🏦</span>
                    <span class="radio-title">Transfer</span>
                </div>
            </label>

            <!-- Option: Cash -->
            <label class="radio-card">
                <input type="radio" name="jenis_transfer" value="Cash" @checked($currentType === 'Cash')>
                <div class="radio-content">
                    <span class="radio-icon">💵</span>
                    <span class="radio-title">Cash</span>
                </div>
            </label>
        </div>
    </div>
    <div class="field"><label for="tanggal_transfer">Tanggal Pembayaran</label><input type="date" class="input"
            id="tanggal_transfer" name="tanggal_transfer" required
            value="{{ old('tanggal_transfer', $transferDateValue ?? $dateValue ?? null) }}">
    </div>
    <div class="field"><label for="nominal">Nominal (Rp)</label><input type="number" class="input" id="nominal"
            name="nominal" min="0" required value="{{ old('nominal', $numericValue ?? null) }}"></div>
    <div class="field"><label for="jam_mulai">Jam Mulai</label><input type="time" class="input" id="jam_mulai"
            name="jam_mulai" required value="{{ old('jam_mulai', $timeValue ?? null) }}"></div>
    <div class="field"><label for="jam_selesai">Jam Selesai</label><input type="time" class="input" id="jam_selesai"
            name="jam_selesai" required value="{{ old('jam_selesai', $endTimeValue ?? null) }}"></div>
    <div class="field" style="grid-column:1/-1;"><label for="gambar_bukti">Bukti Transfer</label><input type="file"
            class="input" id="gambar_bukti" name="gambar_bukti" accept="image/*"><small>JPG/PNG, maksimal 4MB.</small>
    </div>
    <div class="field" style="grid-column:1/-1;"><label for="catatan">Catatan</label><textarea class="input"
            id="catatan" name="catatan" rows="3">{{ old('catatan', $notes ?? null) }}</textarea></div>
</div>