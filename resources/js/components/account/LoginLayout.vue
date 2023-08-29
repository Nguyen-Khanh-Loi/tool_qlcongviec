<script>
export default {
    created() {
        document.body.classList.add("auth-body-bg");
    },
    methods: {},
    mounted() {},
};
</script>
<template>
    <div class="login_register">
        <div class="home-btn d-none d-sm-block">
            <a href="/">
                <i class="mdi mdi-home-variant h2 text-white"></i>
            </a>
        </div>
        <div>
            <div class="container-fluid p-0">
                <div class="row no-gutters">
                    <div class="col-lg-4">
                        <div
                            class="authentication-page-content d-flex align-items-center min-vh-100"
                        >
                            <div class="w-100">
                                <div class="row justify-content-center">
                                    <router-view></router-view>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 ps-0">
                        <div class="authentication-bg">
                            <div class="bg-overlay"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
