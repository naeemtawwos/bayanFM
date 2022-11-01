<template>
  <form :class="{ error: failed }" data-testid="login-form" @submit.prevent="register">
    <div class="logo">
      <img alt="Koel's logo" src="@/../img/logo.svg" width="156">
    </div>

    <input v-model="newUser.name" autofocus placeholder="User Name" required >
    <p class="error" v-if="nameerror">{{nameerror}}</p>
    <input v-model="newUser.email" autofocus placeholder="Email Address" required type="email">
    <p class="error" v-if="emailerror">{{emailerror}}</p>
    <input v-model="newUser.password" placeholder="Password" required type="password">
    <p class="error" v-if="passworderror">{{passworderror}}</p>
    <input v-model="newUser.password_confirmation" placeholder="Confirm Password" required type="password">
    <Btn type="submit">Register</Btn>
    <Btn @click="$emit('showLogin')" green>Sign-in</Btn>

  </form>
</template>

<script lang="ts" setup>
import { reactive, ref } from 'vue'
import { RegisterUserData,  userStore } from '@/stores'
import { parseValidationError, requireInjection  } from '@/utils'
import { DialogBoxKey} from '@/symbols'
import Btn from '@/components/ui/Btn.vue'



// const url = ref('')
// const email = ''
// const password = ''
const failed = ref(false)
// const username= ''
// const password_confirmation = ''
const emit = defineEmits(['loggedin', 'showLogin'])
const dialog = requireInjection(DialogBoxKey)
let nameerror = ref('')
let emailerror = ref('')
let passworderror = ref('')
let errors = [];
const emptyUserData: RegisterUserData = {
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  is_admin: false
}

const newUser = reactive<RegisterUserData>(Object.assign({}, emptyUserData))

const register = async () => {

  try {

    await userStore.register(newUser)
    failed.value =false
    emit('loggedin')

  } catch (err: any) {
    failed.value = true
    const msg = err.response.status === 422 ? parseValidationError(err.response.data)[0] : 'Unknown error.'
    if(err.response.status === 422) {
      emailerror.value = err.response.data.errors.email ? err.response.data.errors.email.join() : ''
      passworderror.value = err.response.data.errors.password ? err.response.data.errors.password.join() : ''
      nameerror.value = err.response.data.errors.name ? err.response.data.errors.name.join() : ''
    }
    console.log(err.response.data.errors)

  }
}
</script>


<style lang="scss" scoped>
/**
 * I like to move it move it
 * I like to move it move it
 * I like to move it move it
 * You like to - move it!
 */
@keyframes shake {
  8%, 41% {
    transform: translateX(-10px);
  }
  25%, 58% {
    transform: translateX(10px);
  }
  75% {
    transform: translateX(-5px);
  }
  92% {
    transform: translateX(5px);
  }
  0%, 100% {
    transform: translateX(0);
  }
}

form {
  width: 280px;
  padding: 1.8rem;
  background: rgba(255, 255, 255, .08);
  border-radius: .6rem;
  border: 1px solid transparent;
  transition: .5s;
  display: flex;
  flex-direction: column;
  gap: 1rem;

  &.error {
    border-color: var(--color-red);
    animation: shake .5s;
  }

  .logo {
    text-align: center;
  }

  .error{
    color: pink;
  }

  @media only screen and (max-width: 414px) {
    border: 0;
    background: transparent;
  }
}
</style>
