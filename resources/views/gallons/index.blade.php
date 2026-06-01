@extends('layouts.app')

@section('title', 'Stok Galon')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    @include('shared.alerts')
    <div class="ibox">
        <div class="ibox-title"><h5>Stok Galon</h5></div>
        <div class="ibox-content">
            <form method="POST" action="{{ route('gallons.store') }}" class="row m-b">@csrf
                <div class="col-sm-4"><input name="name" class="form-control" placeholder="Nama galon" required></div>
                <div class="col-sm-2"><input name="stock" type="number" class="form-control" value="0" required></div>
                <div class="col-sm-2"><label><input type="checkbox" name="is_active" value="1" checked> Aktif</label></div>
                <div class="col-sm-2"><button class="btn btn-primary"><i class="fa fa-plus"></i> Tambah</button></div>
            </form>
            <table class="table table-striped">
                <thead><tr><th>Nama</th><th>Stok</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @foreach($gallons as $gallon)
                        <tr>
                            <form method="POST" action="{{ route('gallons.update', $gallon) }}">@csrf @method('PUT')
                                <td><input name="name" class="form-control" value="{{ $gallon->name }}"></td>
                                <td><input name="stock" type="number" class="form-control" value="{{ $gallon->stock }}"></td>
                                <td><label><input type="checkbox" name="is_active" value="1" @checked($gallon->is_active)> Aktif</label></td>
                                <td class="text-right">
                                    <button class="btn btn-primary btn-sm"><i class="fa fa-save"></i></button>
                            </form>
                                    <form method="POST" action="{{ route('gallons.destroy', $gallon) }}" style="display:inline">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button></form>
                                </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $gallons->links() }}
        </div>
    </div>
</div>
@endsection
