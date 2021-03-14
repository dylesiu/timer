import axios from "axios";
import router from "../router";
import EventBus from "./bus";

const instance = axios.create({
  withCredentials: true,
  baseURL: process.env.VUE_APP_API_URL
});

instance.interceptors.response.use(
  res => res,
  (error) => {
    if (error.response.status === 401 && localStorage.getItem('app_user') !== null) {

      EventBus.$emit('new_notification', {
        'text': 'Twoja sesja wygasła. Proszę, zaloguj się ponownie.',
        'color': 'warning'
      });

      localStorage.removeItem('app_user');
      router.push('/login');
    }

    return Promise.reject(error);
  });

export default instance;
