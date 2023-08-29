<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';

export default {
    data() {
        return {
            loginData: {
                email: '',
                password: '',
                submitted: false
            },
            email: "admin@themesdesign.in",
            password: "123456",
            submitted: false
        };
    },
    computed: {
        ...mapGetters({
            loginResponse: 'getLoginResponse',
            getUser: 'getUser',
            getAge: 'getAge',
        })
    },
    created() {
        document.body.classList.add("auth-body-bg");
    },
    methods: {
        ...mapActions(['login']),

        loginSubmit() {
            this.login(this.loginData);
        },

        showPassword() {
            let password = document.querySelector('#password');
            if (password.type == 'password') {
                password.type = 'text';
            } else {
                password.type = 'password';
            }
        },
    },
    mounted() {
    }
};
</script>
<style lang="scss">   
    .col-1, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-10, .col-11, .col-12, .col, .col-auto, .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm, .col-sm-auto, .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12, .col-md, .col-md-auto, .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg, .col-lg-auto, .col-xl-1, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl, .col-xl-auto {
        position: relative;
    } 
</style>
<template>
    <div>
        <div class="home-btn d-none d-sm-block">
            <a href="/">
                <i class="mdi mdi-home-variant h2 text-white"></i>
            </a>
        </div>
        <div>
            <div class="container-fluid p-0">
                <div class="row g-0">
                    <div class="col-lg-4">
                        <div class="authentication-page-content p-4 d-flex align-items-center min-vh-100">
                            <div class="w-100">
                                <div class="row justify-content-center">
                                    <div class="col-lg-9">
                                        <div>
                                            <div class="text-center">
                                                <div>
                                                    <a href="/" class="logo">
                                                        <img src="../../../images/logo.png" height="20" alt="logo" />
                                                    </a>
                                                </div>

                                                <h4 class="font-size-18 mt-4">Welcome Back !</h4>
                                                <p class="text-muted">Sign in to continue to Nazox.</p>
                                            </div>

                                            <b-alert variant="danger" class="mt-3">
                                            </b-alert>

                                            <div class="p-2 mt-5">
                                                <form class="form-horizontal" @submit.prevent="loginSubmit">
                                                    <div class="form-group auth-form-group-custom mb-4">
                                                        <i class="ri-mail-line auti-custom-input-icon"></i>
                                                        <label for="email">Email</label>
                                                        <input type="email" v-model="loginData.email" class="form-control" id="email"
                                                            placeholder="Enter email"
                                                            :class="{ 'is-invalid': submitted && $v.email.$error }" />
                                                        <div v-if="submitted && $v.email.$error" class="invalid-feedback">
                                                            <span v-if="!$v.email.required">Email is required.</span>
                                                            <span v-if="!$v.email.email">Please enter valid email.</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group auth-form-group-custom mb-4">
                                                        <i class="ri-lock-2-line auti-custom-input-icon"></i>
                                                        <label for="userpassword">Password</label>
                                                        <input v-model="loginData.password" type="password" class="form-control"
                                                            id="userpassword" placeholder="Enter password"
                                                            :class="{ 'is-invalid': submitted && $v.password.$error }" />
                                                        <div v-if="submitted && !$v.password.required"
                                                            class="invalid-feedback">Password is required.</div>
                                                    </div>

                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="customControlInline" />
                                                        <label class="custom-control-label"
                                                            for="customControlInline">Remember me</label>
                                                    </div>

                                                    <div class="mt-4 text-center">
                                                        <button class="btn btn-primary w-md waves-effect waves-light"
                                                            type="submit">Log In</button>
                                                    </div>

                                                    <div class="mt-4 text-center">
                                                        <router-link tag="a" to="/forgot-password" class="text-muted">
                                                            <i class="mdi mdi-lock mr-1"></i> Forgot your password?
                                                        </router-link>
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="mt-5 text-center">
                                                <!-- <p>
                                                    Don't have an account ?
                                                    <router-link tag="a" to="/register"
                                                        class="font-weight-medium text-primary">Register</router-link>
                                                </p> -->
                                                <p>
                                                    © 2020 Nazox. Crafted with
                                                    <i class="mdi mdi-heart text-danger"></i> by Themesdesign
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="authentication-bg">
                            <div class="bg-overlay"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>