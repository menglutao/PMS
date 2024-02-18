<template>
  <div class="password-strength-bar">
    <div :class="barClass" :style="barStyle"></div>
  </div>
</template>

<script>
export default {
  name: 'PasswordStrengthBar',
  props: {
    strength: {
      type: String,
      default: ''
    }
  },
  computed: {
    barClass() {
      return {
        'bar-weak': this.strength === 'Weak',
        'bar-medium': this.strength === 'Medium',
        'bar-strong': this.strength === 'Strong'
      };
    },
    barStyle() {
      switch (this.strength) {
        case 'Weak':
          return { width: '33%' };
        case 'Medium':
          return { width: '66%' };
        case 'Strong':
          return { width: '100%' };
        default:
          return { width: '0%' };
      }
    }
  }
};
</script>

<style scoped>
.password-strength-bar {
  background-color: #eee;
  border-radius: 4px;
  height: 10px;
  width: 9%;
  /* margin-top: 10px; */
  
}

.bar-weak {
  background-color: red;
}

.bar-medium {
  background-color: orange;
}

.bar-strong {
  background-color: green;
}

div {
  height: 100%;
  border-radius: 4px;
}
</style>
