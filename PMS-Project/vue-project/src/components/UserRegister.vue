<template>
  <div id="register">
    <h1>{{ message }}</h1>
    <form @submit.prevent="addUser" class="center">
      <div class="input-container">
        <input v-model="newUser.userName" placeholder="user name" />
      </div>
      <div class="input-container">
        <input v-model="newUser.email" placeholder="Email" type="email"/>
      </div>
      <div class="input-container">
        <input
          v-model="newUser.password"
          type="password"
          placeholder="Password"
          @input="updatePasswordStrength"
        />
        <div>
          Password strength:
          <password-strength-bar
            :strength="passwordStrengthResult"
            class="center"
          />
        </div>
      </div>
      
      <div class="input-container">
        <input v-model="newUser.age" placeholder="Age" type="tel"/>
      </div>
      <div class="input-container">
        <input v-model="newUser.weight" placeholder="Weight(kg)" />
      </div>
      <div class="input-container">
        <input v-model="newUser.height" placeholder="Height(cm)" />
      </div>
      <div>
        <button>Submit</button>
      </div>
    </form>
    <br />
    
  </div>
</template>

<script>
import axios from "axios";
// import CryptoJS from 'crypto-js';
import { passwordStrength } from "check-password-strength";
import PasswordStrengthBar from "./PasswordStrengthBar.vue";


export default {
  data() {
    return {
      message: "Registration Form",
      baseUrl: "http://localhost:8000/register.php",
      uploadUrl: "http://localhost:8000/upload.php",
      checkBox: "",
      selectedUserName: "",
      selectedUser: null,
      users: [],
      newUser: {
        userName: "",
        email: "",
        password: "",
        age: "",
        weight: "",
        height: "",
      },
      passwordStrengthResult: null,
      file: null, // To store the selected file
    };
  },
  methods: {
    addUser() {
      try {
        axios
          .post(this.baseUrl, this.newUser)
          .then((response) => {
            console.log(response);
            if (response.data.success) {
              // forward to login page
              this.$router.push({ name: 'UserLogin' })
              alert("User added successfully");
            } else {
              alert("failure adding user");
            }
          })
          .catch((error) => {
            console.log(error);
          });
      } catch (error) {
        console.error("Error encrypting password:", error.message);
        return null;
      }
    },
    updatePasswordStrength() {
      const password = this.newUser.password;
      if (password) {
        const strength = passwordStrength(password);
        this.passwordStrengthResult = strength.value;
      } else {
        this.passwordStrengthResult = null;
      }
    },
  },
  components: {
    PasswordStrengthBar,
  },
};
</script>

<style>

.input-container {
  display: block;
  margin-bottom: 10px;
}
.image-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
  border: 1px solid #ddd; /* Gray border */
  border-radius: 8px; /* Optional: rounds the corners of the form border */
  max-width: 400px; /* Adjust as needed */
  margin: auto;
}

.center {
  margin-left: auto;
  margin-right: auto;
}

table {
  width: 80%;
  border-collapse: collapse;
}
th,
td {
  border: 1px solid black;
  padding: 8px;
  text-align: center;
}
th {
  background-color: #f2f2f2;
}
a,
button {
  color: #4fc08d;
}

button {
  background: none;
  border: solid 1px;
  border-radius: 2em;
  font: inherit;
  padding: 0.5em 1em;
}
</style>
