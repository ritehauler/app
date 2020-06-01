
@include(config('panel.DIR').'login_header')



        <!-- New Login Design Start -->
<div id="main">
    <div class="login-theme02 admin-form" id="login-form">
        <div class="login-wrap">
            <div class="left-panel light-color">
                <div class="logo-holder"><img src="{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/img/logos/admin-logo-white.png" class="logo" width="250px"></div>
                <div class="signup-carousel slider-carousel">
                    <div class="f-s-slide slide1" style="background:url('{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/client/slider-signin01.jpg');">
                        <div class="gradient-overlay"></div>
                        <div class="f-s-wrap">
                            <div class="f-s-cont">
                                <h2>Keep Calm, Try Managly</h2>
                                <p>World is a land of opportunities and with advent of digital world we have increased our chances to harness their opportunities and smoothen the edges of their rough diamonds.</p>
                            </div>
                        </div>
                    </div>
                    <div class="f-s-slide slide2" style="background:url('{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/client/slider-signin02.jpg');">
                        <div class="gradient-overlay"></div>
                        <div class="f-s-wrap">
                            <div class="f-s-cont">
                                <h2>3 Ways to Increase Transparency with Remote Teams</h2>
                                <p>World is a land of opportunities and with advent of digital world we have increased our chances to harness their opportunities and smoothen the edges of their rough diamonds.</p>
                            </div>
                        </div>
                    </div>
                    <div class="f-s-slide slide3" style="background:url('{!! \URL::to(config('panel.DIR_PANEL_RESOURCE')) !!}/assets/client/slider-signin03.jpg');">
                        <div class="gradient-overlay"></div>
                        <div class="f-s-wrap">
                            <div class="f-s-cont">
                                <h2>Why focus more on building team than company</h2>
                                <p>World is a land of opportunities and with advent of digital world we have increased our chances to harness their opportunities and smoothen the edges of their rough diamonds.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right-panel light-color">
                <div class="d-v-table">
                    <div class="d-v-cell">
                        <div class="tab-content text-center">
                            <div class="login-form tab-pane fade in active text-center" role="tabpanel" id="login-holder">
                                <h2 class="login-header">{!! trans('panel.enter_your_change_password_details_below') !!}</h2>


                                <!-- Login Panel/Form -->
                                @include(config('panel.DIR').'flash_message')

                                <form method="post" action="" class="login-area" name="data_form">

                                    <div class="section mb15 hide">
                                        <label for="verification_token" class="field prepend-icon">
                                            <input class="form-control" type="text" name="verification_token" value="0f8e677d9a2f89a2abda17f08a9170ff">
                                            <label for="verification_token" class="field-icon"> <i class="fa fa-user"></i> </label>
                                            <div id="error_msg_verification_token" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
                                        </label>
                                    </div>
                                    <div class="section mb20">
                                        <label for="password" class="field prepend-icon">
                                            <input class="form-control" type="password" name="password" value="" placeholder="New Password">
                                            <label for="password" class="field-icon"> <i class="fa fa-lock"></i> </label>
                                            <div id="error_msg_password" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
                                        </label>
                                    </div>
                                    <div class="section mb20">
                                        <label for="confirm_password" class="field prepend-icon">
                                            <input class="form-control" type="password" name="confirm_password" value="" placeholder="Confirm Password">
                                            <label for="confirm_password" class="field-icon"> <i class="fa fa-lock"></i> </label>
                                            <div id="error_msg_confirm_password" class="help-block text-right animated fadeInDown hide" style="color:red"></div>
                                        </label>
                                    </div>



                                    <div class="mb30">
                                        <button type="submit" class="button btn-x-wide btn-dark">Submit</button>
                                    </div>
                                    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                                    <input type="hidden" name="email" value="{!! $email !!}"/>
                                    <input type="hidden" name="entity_type_id" value="{!! $entity_type_id !!}"/>
                                    <input type="hidden" name="do_post" value="1">
                                </form>
                                {{--<form method="post" action="" class="forgot-area" name="data_form">
                                    <div class="section">
                                        <label for="current_password" class="field prepend-icon">
                                            <input type="email" name="email" value="{!! $email !!}" class="gui-input"
                                                   placeholder="{!! trans('panel.email') !!}">
                                            <label for="email" class="field-icon"> <i class="fa fa-user"></i> </label>
                                            <div id="error_msg_email" class="help-block text-right animated fadeInDown hide"
                                                 style="color:red"></div>
                                        </label>
                                    </div>
                                    <div class="section">
                                        <label for="new_password" class="field prepend-icon">
                                            <input type="text" name="verification_token" class="gui-input"
                                                   value="{!! $verification_token !!}" placeholder="Verification Token">
                                            <label for="verification_token" class="field-icon"> <i class="fa fa-user"></i> </label>
                                        </label>
                                        <div id="error_msg_verification_token"
                                             class="help-block text-right animated fadeInDown hide"
                                             style="color:red"></div>
                                    </div>
                                    <div class="mb30">
                                        <button type="submit"
                                                class="button btn-x-wide btn-dark">{!! trans('panel.submit') !!}</button>
                                    </div>
                                    <input type="hidden" name="_token" value="{!! csrf_token() !!}"/>
                                    <input type="hidden" name="do_post" value="1"/>
                                </form>--}}

                                <!-- Registration Links -->


                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- END Reminder Content -->

<!-- Reminder Footer -->
<!-- END Reminder Footer -->
@include(config('panel.DIR').'login-footer')