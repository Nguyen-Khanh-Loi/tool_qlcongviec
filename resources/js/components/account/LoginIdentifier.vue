<script>
import { mapGetters, mapMutations, mapActions } from "vuex";

export default {
    data() {
        return {
            loginData: {
                email: this.$route.query.email ?? "admin@gmail.com",
            },
            publicPath : process.env.PUBLIC_URL
        };
    },

    computed: {},
    created() {
        document.body.classList.add("auth-body-bg");
    },
    methods: {
        checklogin({ commit, getters }) {
            axios
                .post("/api/check-login", this.loginData)
                .then((response) => {
                    if (response.data.success === true) {
                        this.userExist = response.data.data.is_user;
                        this.$router.push({
                            name: "Login Challenge",
                            params: {
                                email: this.loginData.email,
                                is_first_login:
                                    response.data.data.is_first_login || 0,
                            },
                        });
                    } else {
                        alert(`Error ${response.data.message}`);
                    }
                })
                .catch((error) => {
                    alert(
                        `Error ${error.response.status}: ${error.response.data.message}`
                    );
                })
                .finally();
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
                <!-- <p class="text-muted">Sign in to continue to Nazox.</p> -->
                <!-- <p>NewName: {{ $store.getters['myModule/getAge'] }}</p>
                                                              <p>UserName: {{ $store.getters['myModule/getUser'] }}</p> -->
            </div>

            <!-- <b-alert variant="danger" class="mt-3"> </b-alert> -->

            <div class="p-2 mt-5">
                <form class="form-horizontal" @submit.prevent="checklogin">
                    <div class="form-group auth-form-group-custom mb-4">
                        <i class="ri-mail-line auti-custom-input-icon"></i>
                        <label for="email">Email</label>
                        <input
                            type="email"
                            v-model="loginData.email"
                            class="form-control"
                            id="email"
                            placeholder="Enter email"
                        />
                    </div>
                    <div class="mt-4 text-center">
                        <button
                            class="btn btn-primary w-md waves-effect waves-light"
                            type="submit"
                        >
                            Next
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-5 text-center">
                <!-- <p>
                  Don't have an account ?
                  <router-link :to="{ name: 'register' }" class="font-weight-medium text-login-register">Register</router-link>
                </p> -->
                <p>
                    © 2023 Crafted with
                    <i class="mdi mdi-heart text-danger"></i> by GOSUTECH
                </p>
            </div>
        </div>
    </div>
</template>
