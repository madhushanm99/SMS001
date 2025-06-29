<x-layout title="Dashboard">


    <section class="section error-404 min-vh-100 d-flex flex-column align-items-center justify-content-center">
        <h1>403</h1>
        <h2>You don't have Access for the page you are looking for.</h2>
        <a class="btn" href="{{ route('dashboard') }}">Back Dashboard</a>
        <img src="assets/img/not-found.svg" class="img-fluid py-5" alt="Page Not Found">
      </section>
  </x-layout>
