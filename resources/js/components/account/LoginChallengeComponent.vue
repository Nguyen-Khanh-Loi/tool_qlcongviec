<script>
import { mapGetters, mapMutations, mapActions } from "vuex";
export default {
    data() {
        return {
            loginData: {
                email: this.$route.params.email,
                is_first_login: this.$route.params.is_first_login,
            },
            publicPath : process.env.PUBLIC_URL
        };
    },
    computed: {
        ...mapGetters({
            loginResponse: "getLoginResponse",
            getUser: "getUser",
            getAge: "getAge",
        }),
    },
    created() {
        document.body.classList.add("auth-body-bg");
    },
    methods: {
        ...mapActions(["firstLogin", "login"]),
        backLogin() {
            this.$router.push({
                name: "login",
                query: {
                    email: this.loginData.email,
                },
            });
        },
        firstLoginSubmit() {
            this.firstLogin(this.loginData);
        },

        loginSubmit() {
            this.login(this.loginData);
        },

        showPassword() {
            let password = document.querySelector("#userpassword");
            if (password.type == "password") {
                password.type = "text";
            } else {
                password.type = "password";
            }
        },
    },
    mounted() {},
};
</script>
<template>
    <div class="col-lg-9">
        <div>
            <div class="text-center">
                <div>
                    <a href="/" class="logo">
                        <img
                            :src="`${publicPath+'/images/logo.png'}`"
                            height="50"
                            alt="logo"
                        />
                    </a>
                </div>

                <h4 class="font-size-18 mt-4">Welcome Back !</h4>
                <!-- <div class="form-group"> -->
                <router-link
                    :to="{ name: 'login', query: { email: loginData.email } }"
                    class="login-challenge-email form-control"
                    ><i class="ri-user-line align-middle mr-1"></i
                    >{{ loginData.email }}</router-link
                >
            </div>
            <div class="p-2 mt-5">
                <form
                    v-if="this.$route.params.is_first_login == 0"
                    class="form-horizontal"
                    @submit.prevent="loginSubmit"
                >
                    <div
                        class="form-group auth-form-group-custom mb-4"
                        style="display: none"
                    >
                        <i class="ri-mail-line auti-custom-input-icon"></i>
                        <!-- <label for="email">Email</label> -->
                        <input
                            type="email"
                            v-model="loginData.email"
                            class="form-control"
                            id="email"
                            placeholder="Enter email"
                            required
                            disabled
                        />
                    </div>

                    <div class="form-group auth-form-group-custom mb-4">
                        <i class="ri-lock-2-line auti-custom-input-icon"></i>
                        <label for="userpassword">Password</label>
                        <input
                            v-model="loginData.password"
                            type="password"
                            class="form-control"
                            id="userpassword"
                            placeholder="Enter password"
                            required
                        />
                    </div>

                    <div class="custom-control custom-checkbox">
                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="customControlInline"
                        />
                        <label
                            class="custom-control-label"
                            for="customControlInline"
                        >
                            Remember me
                        </label>
                    </div>

                    <div class="mt-4 text-center">
                        <button
                            class="btn btn-primary w-md waves-effect waves-light"
                            type="submit"
                        >
                            Log In
                        </button>
                    </div>
                </form>
                <form
                    v-else
                    class="form-horizontal"
                    @submit.prevent="firstLoginSubmit"
                >
                    <div
                        class="form-group auth-form-group-custom mb-4"
                        style="display: none"
                    >
                        <i class="ri-mail-line auti-custom-input-icon"></i>
                        <label for="email">Email</label>
                        <input
                            type="email"
                            v-model="loginData.email"
                            class="form-control"
                            id="email"
                            placeholder="Enter email"
                            required
                        />
                    </div>

                    <div class="form-group auth-form-group-custom mb-4">
                        <i class="ri-lock-2-line auti-custom-input-icon"></i>
                        <label for="userpassword">Password</label>
                        <input
                            v-model="loginData.password"
                            type="password"
                            class="form-control"
                            id="user-password"
                            placeholder="Enter password"
                            required
                        />
                    </div>
                    <div class="form-group auth-form-group-custom mb-4">
                        <i class="ri-lock-2-line auti-custom-input-icon"></i>
                        <label for="userrepassword">Repassword</label>
                        <input
                            v-model="loginData.repassword"
                            type="password"
                            class="form-control"
                            id="user-repassword"
                            placeholder="Enter password"
                            required
                        />
                    </div>

                    <div class="custom-control custom-checkbox">
                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="customControlInline"
                        />
                        <label
                            class="custom-control-label"
                            for="customControlInline"
                        >
                            Remember me
                        </label>
                    </div>

                    <div class="mt-4 text-center">
                        <button
                            class="btn btn-primary w-md waves-effect waves-light"
                            type="submit"
                        >
                            Log In
                        </button>
                    </div>
                </form>
            </div>
            <div class="mt-5 text-center">
                <p>
                    © 2023 Crafted with
                    <i class="mdi mdi-heart text-danger"></i> by GOSUTECH
                </p>
            </div>
        </div>
    </div>
</template>
