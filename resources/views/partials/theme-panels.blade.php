{{-- Wishlist and register panels so theme scripts (scripts.js) work without errors. --}}
<!--wish-list-wrap-->
<div class="wish-list-wrap">
    <div class="wish-list-close clwl_btn"><i class="fa-regular fa-xmark"></i></div>
    <div class="wish-list_header">
        <div class="wish-list-title">Your Wishlist <span>0</span></div>
    </div>
    <div class="wish-list-container" id="wishlist-panel-container">
        <p class="p-3 text-muted mb-0">No items yet.</p>
    </div>
    <div class="wish-list-footer">
        <div class="clear_wishlist">Clear Wishlist</div>
    </div>
</div>
<div class="mob-nav-overlay fs-wrapper"></div>
<div class="body-overlay fs-wrapper wishlist-wrap-overlay clwl_btn"></div>

<!--register form-->
<div class="main-register-container">
    <div class="main-register_box">
        <div class="main-register-holder">
            <div class="main-register-wrap">
                <div class="main-register_bg">
                    <div class="mr_title">
                        <h4>Welcome to {{ config('app.name') }}</h4>
                        <h5>Sign in or register</h5>
                    </div>
                    <div class="main-register_contacts-wrap">
                        <h4>Have a question?</h4>
                        <a href="#">Get in Touch</a>
                        <div class="svg-corner svg-corner_white hero-corner-bl"></div>
                        <div class="svg-corner svg-corner_white hero-corner-br"></div>
                    </div>
                    <div class="main-register_bg-dec"></div>
                </div>
                <div class="main-register tabs-act fl-wrap">
                    <ul class="tabs-menu">
                        <li class="current"><a href="#tab-1"><i class="fa-regular fa-sign-in-alt"></i> Login</a></li>
                        <li><a href="#tab-2"><i class="fa-regular fa-user-plus"></i> Register</a></li>
                    </ul>
                    <div class="close-modal close-reg-form"><i class="fa-regular fa-xmark"></i></div>
                    <div id="tabs-container">
                        <div class="tab">
                            <div id="tab-1" class="tab-content first-tab">
                                <div class="custom-form">
                                    <form method="post" action="#" name="registerform">
                                        @csrf
                                        <div class="cs-intputwrap">
                                            <i class="fa-light fa-user"></i>
                                            <input type="text" placeholder="Username or Email Address" value="">
                                        </div>
                                        <div class="cs-intputwrap pass-input-wrap">
                                            <i class="fa-light fa-lock"></i>
                                            <input type="password" class="pass-input" placeholder="Password" value="">
                                            <div class="view-pass"></div>
                                        </div>
                                        <div class="filter-tags">
                                            <input id="check-a" type="checkbox" name="check" checked>
                                            <label for="check-a">Remember me</label>
                                        </div>
                                        <div class="lost_password"><a href="#">Lost Your Password?</a></div>
                                        <div class="clearfix"></div>
                                        <button type="submit" class="commentssubmit">Log In</button>
                                    </form>
                                </div>
                            </div>
                            <div id="tab-2" class="tab-content">
                                <div class="custom-form">
                                    <form method="post" action="#" name="registerform" class="main-register-form">
                                        @csrf
                                        <div class="cs-intputwrap">
                                            <i class="fa-light fa-user"></i>
                                            <input type="text" placeholder="Full Name" value="">
                                        </div>
                                        <div class="cs-intputwrap">
                                            <i class="fa-light fa-envelope"></i>
                                            <input type="text" placeholder="Email Address" value="">
                                        </div>
                                        <div class="cs-intputwrap pass-input-wrap">
                                            <i class="fa-light fa-lock"></i>
                                            <input type="password" class="pass-input" placeholder="Password" value="">
                                            <div class="view-pass"></div>
                                        </div>
                                        <button type="submit" class="commentssubmit"><span>Register</span></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="log-separator fl-wrap"><span>or</span></div>
                        <div class="soc-log fl-wrap">
                            <p>Use your social account.</p>
                            <a href="#" class="google_log"><i class="fa-brands fa-google"></i> Connect with Google</a>
                            <a href="#" class="fb_log"><i class="fa-brands fa-facebook-f"></i> Connect with Facebook</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="body-overlay fs-wrapper reg-overlay close-reg-form"></div>
</div>
