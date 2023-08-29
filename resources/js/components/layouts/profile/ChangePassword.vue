<template>
  <div class="profile">
    <div class="row">
      <div class="col-md-4">
        <div class="card show border border-0 profile-card-body">
          <div class="card-body p-0">
            <div class="row col-md-12 m-0 p-0 overflow-hidden background-avatar">
              <img src="/images/background-avatar.png" alt="" class="p-0 rounded-top">
            </div>
            <div class="avatar row col-md-12 m-0 p-0 d-flex justify-content-center position-absolute">
              <img src="/images/avatar.png" alt="">
            </div>
            <div class="description-profile">
              <div class="form-group px-5">
                <i class="fas fa-user text-secondary mx-3"></i>
                <router-link :to="{ name: 'Profile User' }" class="text-secondary">Profile</router-link>
              </div>
              <div class="form-group px-5 pb-3 mt-3">
                <i class="fas fa-lock text-secondary mx-3"></i>
                <router-link :to="{ name: 'Change Password' }" class="text-secondary">Change Password</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card show border border-0 profile-card-body">
          <div class="card-body">
            <h4 class="card-title text-center fs-4">Change Password</h4>
            <div class="col-md-12 mt-3">
              <form @submit.prevent="updatePasswordUser">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="label-item">Old Password</label>
                      <input type="password" class="form-control mt-1" required v-model="userForm.password" />
                    </div>
                    <div class="form-group mt-2">
                      <label class="label-item">New Password</label>
                      <input type="password" class="form-control mt-1" required v-model="userForm.password_new" />
                    </div>
                    <div class="form-group mt-2">
                      <label class="label-item">Re-enter password</label>
                      <input type="password" class="form-control mt-1" required v-model="userForm.repassword_new" />
                    </div>
                  </div>
                </div>
                <div class="d-flex">
                  <button type="submit" class="btn btn-all-add-edit py-2 px-4 mt-4 btn-primary">
                    Save
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
  
<script>
export default {
  data() {
    return {
      userForm: {},
    };
  },
  methods: {
    updatePasswordUser() {
      this.userForm.email = "admin@gmail.com";
      this.axios
        .post(`/api/user/change-password`, this.userForm)
        .then((response) => {
          if (response.data.status === 200) {
            // this.$swal.fire({
            //   position: "top-end",
            //   icon: "success",
            //   title: `Update Password Success`,
            //   showConfirmButton: false,
            //   timer: this.$config.notificationTimer ?? 1000,
            // });
            alert(response.data.message);
          }
        })
        .catch((error) => {
          this.$swal.fire({
            icon: "error",
            title: "Oops...",
            text: `Error ${error.response.status}: ${error.response.data.message}`,
          });
          // alert(`Error ${error.response.status}: ${error.response.data.message}`);
        })
        .finally(() => (this.loading = false));
    },
  }
};
</script>
  