<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import { required, email, minLength, sameAs } from '@vuelidate/validators';
import { useVuelidate } from '@vuelidate/core'
export default {
    setup: () => ({ v$: useVuelidate() }),    
    data() {
        return {
            userData: { 
                username: "", 
                email: "", 
                password: {
                    password: "",
                    confirm: ""
                } 
            },
        };
    },
    created() {
        document.body.classList.add("auth-body-bg");
    },
    validations() {
        return {
            userData: {
                email: { required, email },
                username: { required },
                password: {
                    password: { required },
                    confirm: { required },
                },
            }
        }
    },
    methods: {
        ...mapActions(['register']),

        registerSubmit() {
            console.log(this.v$);
            // console.log(this.userData)
            // this.register(this.userData);
        },
    },
    mounted() {
        console.log(this.userData)
    }
};
</script>

<template>
    <div>
        <div class="home-btn d-none d-sm-block">
            <a href="/">
                <i class="mdi mdi-home-variant h2 text-white"></i>
            </a>
        </div>
        <div>
            <div class="container-fluid p-0">
                <div class="row no-gutters">
                    <div class="col-lg-4">
                        <div class="authentication-page-content p-4 d-flex align-items-center min-vh-100">
                            <div class="w-100">
                                <div class="row justify-content-center">
                                    <div class="col-lg-9">
                                        <div>
                                            <div class="text-center">
                                                <div>
                                                    <a href="/" class="logo">
                                                        <img src="/images/logo-dark.png" height="20" alt="logo" />
                                                    </a>
                                                </div>

                                                <h4 class="font-size-18 mt-4">Register account</h4>
                                                <p class="text-muted">Get your free Nazox account now.</p>
                                            </div>

                                            <div class="p-2 mt-5">
                                                <form class="form-horizontal" @submit.prevent="registerSubmit">
                                                    <!-- <div class="form-group auth-form-group-custom mb-4">
                                                        <i class="ri-user-2-line auti-custom-input-icon"></i>
                                                        <label for="fname">First name</label>
                                                        <input type="text" class="form-control"
                                                            id="fname"
                                                            placeholder="Enter First name" />
                                                    </div>
                                                    <div class="form-group auth-form-group-custom mb-4">
                                                        <i class="ri-user-2-line auti-custom-input-icon"></i>
                                                        <label for="lname">Last name</label>
                                                        <input type="text" class="form-control"
                                                            id="lname"
                                                            placeholder="Enter Last name" />
                                                    </div> -->
                                                    <div class="form-group auth-form-group-custom mb-4">
                                                        <i class="ri-user-2-line auti-custom-input-icon"></i>
                                                        <label for="username">Username</label>
                                                        <input v-model="userData.username" type="text" class="form-control"
                                                            id="username"
                                                            placeholder="Enter username" />
                                                    </div>
                                                    <div class="form-group auth-form-group-custom mb-4">
                                                        <i class="ri-mail-line auti-custom-input-icon"></i>
                                                        <label for="useremail">Email</label>
                                                        <input v-model="userData.email" type="email" class="form-control"
                                                            id="useremail" placeholder="Enter email" /> 
                                                    </div>
                                                    <div class="form-group auth-form-group-custom mb-4">
                                                        <i class="ri-lock-2-line auti-custom-input-icon"></i>
                                                        <label for="userpassword">Password</label>
                                                        <input v-model="userData.password.password" type="password" class="form-control"
                                                            id="userpassword" placeholder="Enter password"/>
                                                    </div>
                                                    <div class="form-group auth-form-group-custom mb-4">
                                                        <i class="ri-lock-2-line auti-custom-input-icon"></i>
                                                        <label for="userpassword">Confirm Password</label>
                                                        <input v-model="userData.password.confirm" type="password" class="form-control"
                                                            id="cfpasswword" placeholder="Enter password"/>
                                                    </div>
                                                    <!-- <div class="form-group auth-form-group-custom mb-4">
                                                        <i class="ri-lock-2-line auti-custom-input-icon"></i>
                                                        <label for="userpassword">Phone</label>
                                                        <input type="" class="form-control"
                                                            id="phonenumber" placeholder="Enter Phone"/>
                                                    </div>
                                                    <div class="form-group auth-form-group-custom mb-4">
                                                        <i class="ri-lock-2-line auti-custom-input-icon"></i>
                                                        <label for="userpassword">Gender:</label>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="femaleGender" value="option1" checked="" autocompleted="" data-gtm-form-interact-field-id="2">
                                                            <label class="form-check-label" for="femaleGender">Female</label>
                                                        </div>

                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="maleGender" value="option2" autocompleted="" data-gtm-form-interact-field-id="0">
                                                            <label class="form-check-label" for="maleGender">Male</label>
                                                        </div>

                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="otherGender" value="option3" autocompleted="" data-gtm-form-interact-field-id="1">
                                                            <label class="form-check-label" for="otherGender">Other</label>
                                                        </div>
                                                    </div> -->
                                                    <div class="text-center">
                                                        <button class="btn btn-primary w-md waves-effect waves-light"
                                                            type="submit">Register</button>
                                                    </div>

                                                    <div class="mt-4 text-center">
                                                        <p class="mb-0">
                                                            By registering you agree to the Nazox
                                                            <a href="#" class="text-primary">Terms of Use</a>
                                                        </p>
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="mt-5 text-center">
                                                <p>
                                                    Already have an account ?
                                                    <router-link tag="a" to="/login"
                                                        class="font-weight-medium text-primary">Login</router-link>
                                                </p>
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