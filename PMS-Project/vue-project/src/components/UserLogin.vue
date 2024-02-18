<template>
  <div id="login" class="center">
    <h1>Login</h1>
    <form v-if="showLoginForm" @submit.prevent="login" class="center">
      <div>
        <label for="username">Username</label>
        <input type="text" id="username" v-model="userInfor.username" />
      </div>
      <div>
        <label for="password">Password</label>
        <input type="password" id="password" v-model="userInfor.password" />
      </div>
      <button type="submit">Login</button>
    </form>

    <!-- 2FA form -->
    <form v-if="show2FAForm" @submit.prevent="verify2FA" class="center">
      <div>
        <label for="2fa">validation code</label>
        <input type="text" id="2fa" v-model="validationcode" />
      </div>
      <button type="submit">Verify</button>
    </form>
  </div>
</template>
<script>
import axios from "axios";
export default {
  name: "UserLogin",
  data() {
    return {
      showLoginForm: true,
      show2FAForm: false,
      loginUrl: "http://localhost:8000/user-login.php",
      verify2FAUrl: "http://localhost:8000/verify-2fa.php",
      userInfor: {
        username: "",
        password: "",
      },
      validationcode: "", // this is the validation code that the user will enter
      is_password_valid: false,
      true_code: "", // real validation code
    };
  },
  methods: {
    login() { 
      axios
        .post(this.loginUrl, this.userInfor)
        .then((response) => {
          if (response.data.authenticated){
            alert("Login successful");
            this.$router.push({
              name: "HealthLog",
              query: {
                user_name: this.userInfor.username,
              },
            });
          }
          else if (response.data.login) {
            alert("insert validation code");
            this.showLoginForm = false;
            this.show2FAForm = true;
            this.is_password_valid = true;
            this.true_code = response.data.code;
            
          } else {
            console.log("Login failed with message:", response.data.message);
            alert("Login failed");
          }
        })
        .catch((error) => {
          console.error("Login error", error);
          alert("Login failed");
        });
    },
    verify2FA() {
      if (this.is_password_valid) {
        const payload = {
          validationcode: this.validationcode,
          true_code: this.true_code,
        };
        axios
          .post(this.verify2FAUrl, payload)
          .then((response) => {
            if (response.data.success) {
              alert("Login successful");
              this.$router.push({
                name: "HealthLog",
                query: {
                  user_name: this.userInfor.username,

                },
              });
            } else {
                alert("Login failed, the code is wrong");
            }
          })
          .catch((error) => {
            console.error("Login error", error);
            alert("Login failed");
          });
      }
    },
  },
};
</script>

<style>
.center {
  margin-left: auto;
  margin-right: auto;
}
</style>
