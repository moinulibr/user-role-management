<!-- 1. Profile Information Update Form -->

<div class="card card-default mb-4" id="profile">
    <div class="card-header">
        <h2 class="mb-1">Profile Information</h2>
        <p class="mt-1 text-sm text-gray-600">Update your account's profile information and email address.</p>
    </div>

    <div class="card-body">
        <!-- Form for updating Name and Email (uses route('profile.update')) -->
        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="row mb-2">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        {{-- $user ভেরিয়েবলটি মূল ফাইল থেকে আসছে --}}
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                        <!-- Laravel Validation Error Display -->
                        @error('name')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                        @error('email')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
            <!-- Email Verification Check -->
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <p class="text-sm mt-2 text-warning">Your email address is unverified. <a href="#" onclick="event.preventDefault(); document.getElementById('send-verification').submit();" class="text-primary">Click here to re-send the verification email.</a></p>
            @endif
            <!-- End Email Verification Check -->

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary btn-pill">Save Changes</button>
                <!-- Status message (for Alpine.js) -->
                @if(session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-success ml-3 align-self-center">Saved.</p>
                @endif
            </div>
        </form>
        <form id="send-verification" method="post" action="{{ route('verification.send') }}" style="display: none;">
            @csrf
        </form>
    </div>

</div>
<!-- End Profile Information Update Form -->