@extends('layouts.dashboard.app')

@section('content')

    @push('styles')
        <link rel="stylesheet" href="{{asset('web_files/css/bootstrap-fileinput.css')}}">
        <link href="{{asset('dashboard_files/assets/plugins/bootstrap-select/css/bootstrap-select.css')}}"
              rel="stylesheet"/>
    @endpush

    <section class="content">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-5 col-sm-12">
                    <h2>Thêm phim
                        {{-- <small>Welcome to Films</small> --}}
                    </h2>
                </div>
                <div class="col-lg-5 col-md-7 col-sm-12">
                    <ul class="breadcrumb float-md-right">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}"><i class="zmdi zmdi-home"></i>
                                Films</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Films</a></li>
                        <li class="breadcrumb-item active">Add</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Thêm</strong> Phim</h2>
                        </div>

                        <div class="body">
                            <form action="{{route('dashboard.films.store')}}" method="POST"
                                  enctype="multipart/form-data">
                                @csrf

                                <div class="header col-lg-12 col-md-12 col-sm-12">
                                    <h2>Thông tin chính</h2>
                                </div>

                                <div class="row clearfix">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" name="name" class="form-control"
                                                   placeholder="Tên" value="{{ old('name', '') }}">
                                            <span style="color: red; margin-left: 10px">{{ $errors->first('name') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="text" name="year" class="form-control"
                                                   placeholder="Năm sản xuất" value="{{ old('year', '') }}">
                                            <span style="color: red;margin-left: 10px">{{ $errors->first('year') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row clearfix">
                                    <div class="col-sm-12">
                                        <select class="form-control z-index show-tick" name="categories[]" data-live-search="true" multiple>
                                            <option selected disabled>Danh mục: </option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <span style="color: red;margin-left: 10px">{{ $errors->first('categories') }}</span>
                                    </div>
                                </div>
                                <br>
                                <div class="row clearfix">
                                    <div class="col-sm-12">
                                        <select class="form-control z-index show-tick" name="actors[]" data-live-search="true" multiple>
                                            <option selected disabled>Diễn viên: </option>
                                            @foreach ($actors as $actor)
                                                <option value="{{ $actor->id }}">{{ $actor->name }}</option>
                                            @endforeach
                                        </select>
                                        <span style="color: red;margin-left: 10px">{{ $errors->first('actors') }}</span>
                                    </div>
                                </div>

                                <br>
                                <br>

                                <div class="row clearfix">
                                    <div class="header col-lg-12 col-md-12 col-sm-12">
                                        <h2>Tổng quan về phim</h2>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <textarea name="overview" rows="4" class="form-control no-resize"
                                                      placeholder="Mô tả phim">{{ old('overview', '') }}</textarea>
                                            <span style="color: red; margin-left: 10px">{{ $errors->first('overview') }}</span>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="row clearfix">
                                    <div class="header col-lg-12 col-md-12 col-sm-12">
                                        <h2>Video phim</h2>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <textarea name="url" rows="4" class="form-control no-resize"
                                                      placeholder="Embed Code From JWPlayer Server">{{ old('url', '') }}</textarea>
                                            <span style="color: red; margin-left: 10px">{{ $errors->first('url') }}</span>
                                        </div>
                                    </div>
                                </div> --}}

                                @livewire(
                                    'dashboard.films.add-episode', 
                                    ['id' => 0]
                                )

                                <div class="row clearfix">
                                    <div class="col-sm-12">
                                        <div class="header col-lg-12 col-md-12 col-sm-12">
                                            <h2>Phim miễn phí:</h2>
                                        </div>
                                    </div>
                                    <select class="form-control show-tick" name="is_free">
                                        <option value="0">
                                            Có phí
                                        </option>
                                        <option value="1" selected="selected">
                                            Miễn phí
                                        </option>
                                    </select>
                                    <span style="color: red; margin-left: 10px">{{ $errors->first('is_free') }}</span>
                                </div>

                                <div class="row clearfix">
                                    <div class="col-sm-12">
                                        <div class="header col-lg-12 col-md-12 col-sm-12">
                                            <h2>Thành viên xem được phim:</h2>
                                        </div>
                                    </div>
                                    <select class="form-control show-tick" name="memberships_can_see[]" multiple>
                                        @foreach($memberships as $membership)
                                        <option value="{{$membership->id}}">
                                            {{$membership->title}}
                                        </option>
                                        @endforeach
                                    </select>                                    
                                    <span style="color: red; margin-left: 10px">{{ $errors->first('memberships_can_see') }}</span>
                                </div>

                                {{-- <div class="row clearfix">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <textarea name="api_url" rows="4" class="form-control no-resize"
                                                      placeholder="API URL">{{ old('api_url', '') }}</textarea>
                                            <span style="color: red; margin-left: 10px">{{ $errors->first('api_url') }}</span>
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="form-group last mt-5">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail"
                                             style="width: 200px; height: 150px;">
                                            <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image"
                                                 alt=""/>
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail"
                                             style="max-width: 200px; max-height: 150px;">
                                        </div>
                                        <div>
                                                <span class="btn btn-dark btn-file">
                                                    <span class="fileinput-new"> Chọn ảnh bìa </span>
                                                    <span class="fileinput-exists"> Thay đổi </span>
                                                    <input type="file" name="background_cover"
                                                           value="{{ old('background_cover', '') }}">
                                                </span>
                                            <a href="" class="btn btn-danger fileinput-exists"
                                               data-dismiss="fileinput">
                                                Xoá </a>
                                        </div>
                                        <span style="color: red; margin-left: 10px">{{ $errors->first('background_cover') }}</span>
                                    </div>
                                </div>


                                <div class="form-group last">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail"
                                             style="width: 200px; height: 150px;">
                                            <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image"
                                                 alt=""/>
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail"
                                             style="max-width: 200px; max-height: 150px;">
                                        </div>
                                        <div>
                                                <span class="btn btn-dark btn-file">
                                                    <span class="fileinput-new"> Chọn ảnh poster </span>
                                                    <span class="fileinput-exists"> Thay đổi </span>
                                                    <input type="file" name="poster"
                                                           value="{{ old('poster', '') }}">
                                                </span>
                                            <a href="" class="btn btn-danger fileinput-exists"
                                               data-dismiss="fileinput">
                                                Xoá </a>
                                        </div>
                                        <span style="color: red; margin-left: 10px">{{ $errors->first('poster') }}</span>
                                    </div>
                                </div>

                                <div class="row clearfix">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary btn-round">Thêm</button>
                                        <button type="reset" class="btn btn-default btn-round btn-simple">Huỷ bỏ
                                        </button>
                                        <a href={{route('dashboard.films.index')}} class="btn btn-second btn-round">Quay lại</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="{{asset('web_files/js/bootstrap-fileinput.js')}}"></script>
    @endpush

@endsection