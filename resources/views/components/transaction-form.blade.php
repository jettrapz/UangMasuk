<div class="form-grid">
        <div class="field"><label for="nama">Nama</label><input class="input" id="nama" name="nama" required
                        value="{{ old('nama', $value ?? null) }}"></div>
        <div class="field"><label for="tanggal_main">Tanggal Main</label><input type="date" class="input"
                        id="tanggal_main" name="tanggal_main" required
                        value="{{ old('tanggal_main', $dateValue ?? null) }}">
        </div>
        <div class="field">
                <label for="jenis_transfer">Jenis Pembayaran</label>
                <div class="select-wrap">
                        @php $currentType = old('jenis_transfer', $selectedType ?? 'Transfer'); @endphp
                        <select class="input select-enhanced" id="jenis_transfer" name="jenis_transfer" required>
                                <option value="Qris" @selected($currentType === 'Qris')>QRIS</option>
                                <option value="Transfer" @selected($currentType === 'Transfer')>Transfer Bank</option>
                                <option value="Cash" @selected($currentType === 'Cash')>Cash / Tunai</option>
                        </select>
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
                        class="input" id="gambar_bukti" name="gambar_bukti" accept="image/*"><small>JPG/PNG, maksimal
                        4MB.</small>
        </div>
        <div class="field" style="grid-column:1/-1;"><label for="catatan">Catatan</label><textarea class="input"
                        id="catatan" name="catatan" rows="3">{{ old('catatan', $notes ?? null) }}</textarea></div>
</div>