@extends('layouts.dash')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid"><!-- container div -->

    <div class="desktop-header"> <!-- header div -->
        <div class="card card-block topnav-left">
            <div class="card-body write-card">
                <div class="d-flex align-items-center justify-content-between">
                    <h4>{{-- Laravel --}} Test Page</h4>
                    <a href="/dashboard/{{ $guild_id }}" class="btn btn-outline-primary svg-icon">
                        <svg  width="20" class="svg-icon" id="new-note-back" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                        <span>Back</span>
                    </a>
                </div>
            </div>
        </div>
        @include('partials.dash.topnav-right')
    </div>

    <!-- start page content -->
    <div class="row">
            <div class="col-lg-12">  


            </div>
    </div>


</div><!-- end container div -->


@endsection