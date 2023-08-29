<template>
  <div class="profile">
      <div class="row">
          <div class="col-md-4">
              <div class="card show border border-0 profile-card-body">
                  <div class="card-body p-0 card-body-user">
                      <div
                          class="row col-md-12 m-0 p-0 overflow-hidden background-avatar"
                      >
                          <img
                              src="/images/background-avatar.png"
                              alt=""
                              class="p-0 rounded-top"
                          />
                      </div>
                      <div
                          class="avatar row col-md-12 m-0 p-0 d-flex justify-content-center position-absolute"
                      >
                          <span class="wrap-img">
                              <img :alt="authUserData.name" 
                                  :src="`${authUserData.avatar ? publicPath+'users/'+authUserData.avatar : publicPath+avatar}`"
                              />
                          </span>
                          
                          <input
                              type="file"
                              accept="image/*"
                              v-modal="authUserData.avatar"
                              @change="upload"
                              class="choose_avatar"
                          />
                          <!-- <h6 class="text-center py-2">{{ authUserData.name }}</h6> -->
                      </div>
                      <div class="description-profile">
                          <div class="form-group px-5">
                              <i class="fas fa-user text-secondary mx-3"></i>
                              <router-link
                                  :to="{ name: 'Profile User' }"
                                  class="text-secondary"
                                  >Profile</router-link
                              >
                          </div>
                          <div class="form-group px-5 pb-3 mt-3">
                              <i class="fas fa-lock text-secondary mx-3"></i>
                              <router-link
                                  :to="{ name: 'Change Password' }"
                                  class="text-secondary"
                                  >Change Password</router-link
                              >
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <div class="col-md-8">
              <div class="card show border border-0 profile-card-body">
                  <div class="card-body">
                      <h4 class="card-title text-center fs-4">
                          Change Profile
                      </h4>
                      <div class="col-md-12 mt-3">
                          <form @submit.prevent="updateUser">
                              <div class="row">
                                  <div class="col-md-6 col-sm-12">
                                      <div class="form-group">
                                          <label>UserName</label>
                                          <input
                                              type="text"
                                              placeholder="username"
                                              class="form-control mt-1"
                                              disabled
                                              v-model="UserForm.name"
                                          />
                                      </div>
                                      <div class="form-group mt-2">
                                          <label>FirstName</label>
                                          <input
                                              type="text"
                                              placeholder="First Name"
                                              class="form-control mt-1"
                                              v-model="UserForm.first_name"
                                          />
                                      </div>
                                      <div class="form-group mt-2">
                                          <label>Phone Number</label>
                                          <input
                                              type="text"
                                              placeholder="Phone Number"
                                              class="form-control mt-1"
                                              v-model="UserForm.phone"
                                          />
                                      </div>
                                  </div>

                                  <div class="col-md-6 col-sm-12">
                                      <div class="form-group">
                                          <label>Email</label>
                                          <input
                                              type="email"
                                              placeholder="Email"
                                              class="form-control mt-1"
                                              disabled
                                              v-model="UserForm.email"
                                          />
                                      </div>
                                      <div class="form-group mt-2">
                                          <label>LastName</label>
                                          <input
                                              type="text"
                                              placeholder="Last Name"
                                              class="form-control mt-1"
                                              v-model="UserForm.last_name"
                                          />
                                      </div>
                                      <div class="form-group mt-2">
                                          <label>Address</label>
                                          <input
                                              type="text"
                                              placeholder="Address"
                                              class="form-control mt-1"
                                              v-model="UserForm.address"
                                          />
                                      </div>
                                  </div>
                              </div>
                              <div class="d-flex">
                                  <button
                                      type="submit"
                                      class="mt-4 btn-primary btn btn-all-add-edit py-2 px-4"
                                  >
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
import { mapGetters, mapMutations, mapActions } from "vuex";
export default {    
  data() {
      return {
          // UserForm: {},
          avatar: 'images/avatar.png',
          publicPath: process.env.PUBLIC_URL
      };
  },
  computed: {
      authUser() {
          if (this.$store.getters.getAuthUser.id !== undefined) {
              return this.$store.getters.getAuthUser;
          }
          return JSON.parse(sessionStorage.getItem("authUser"));
      },
      UserForm() {
          return {
              name: this.authUser.name,
              email: this.authUser.email,
              first_name: this.authUser.first_name,
              last_name: this.authUser.last_name,
              phone: this.authUser.phone,
              address: this.authUser.address,
          };
      },
      ...mapGetters(["getLoginResponse", "authUserData"]),
  },
  methods: {
      ...mapActions(["storeUpdateUser", "uploadAvatar"]),
     updateUser() {
          this.axios
              .post(`/api/user/update`, this.UserForm)
              .then((response) => {
                  if (response.data.status === 200) {
                      this.storeUpdateUser();
                      this.$emit('updateInfoAuth', response.data.data);                      
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
      async upload(e) {
          let files = e.target.files || e.dataTransfer.files;
          if (!files.length) return;
          this.file = e.target.files[0];
          let formData = new FormData();
          formData.append("file", e.target.files[0]);
          formData.append("user_id", this.authUserData.id);
          formData.append("type", "avatar");
          var results = await this.uploadAvatar(formData);
          this.$emit('updateInfoAuth', results);
      },
  },
};
</script>