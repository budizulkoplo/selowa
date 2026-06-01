<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('transactions.store') }}" id="{{ $formId }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Input Transaksi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Pelanggan</label>
                            <select name="customer_id" id="{{ $customerSelectId }}" class="form-control select2 modal-select2" required>
                                <option value="">Pilih pelanggan</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" data-price="{{ $customer->effectivePrice() }}">{{ $customer->name }} - Rp {{ number_format($customer->effectivePrice(), 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Mobil Berjalan</label>
                            <select name="delivery_run_id" class="form-control select2 modal-select2">
                                <option value="">Tanpa mobil berjalan</option>
                                @foreach($deliveryRuns as $run)
                                    <option value="{{ $run->id }}">{{ $run->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Jumlah Galon</label>
                            <input name="qty" type="number" min="1" class="form-control" value="1" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Harga Satuan</label>
                            <input name="price" id="{{ $priceInputId }}" type="text" inputmode="numeric" class="form-control money-input" placeholder="0" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Waktu Transaksi</label>
                            <input name="created_at" id="{{ $timeInputId }}" type="datetime-local" class="form-control" value="{{ $serverNow }}">
                        </div>
                    </div>
                    <div class="checkbox m-t-none">
                        <label><input type="checkbox" name="use_server_time" id="{{ $useServerId }}" value="1" checked> Gunakan waktu server</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary"><i class="fa fa-save"></i> Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
