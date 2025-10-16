import axios from "axios"
import Cookies from "js-cookie"
import { useUserStore} from "@/store/UserStore";
import config from "../../config";
import Swal from "sweetalert2";

const API_URL = config.BASE_API_URL
var TOKEN = ''
export default class ApiService {
  constructor() {
    TOKEN = useUserStore().token
    if(TOKEN == null){
        TOKEN = Cookies.get("token")
        useUserStore().token = TOKEN
    }
    if(!TOKEN){
        TOKEN = ''
    }
    this.axios = axios.create({
      baseURL: API_URL,
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json", 
        "authorization": "Bearer " + TOKEN
      },
    })
      //quando a request retornar 403, significa que o token expirou, então é exibido um alerta dizendo que não tem autorização
        this.axios.interceptors.response.use(
            response => response,
            async error => {
            const originalRequest = error.config;
            if (error.response.status === 403 && !originalRequest._retry) {
                originalRequest._retry = true;
                Swal.fire({
                    title: 'Ops!',
                    text: 'Você não tem autorização para acessar essa funcionalidade!',
                    icon: 'error',
                    confirmButtonText: 'Ok',
                });
            }
            return Promise.reject(error);
            }
        );
  }

  async get(path, params = {}) {
    const response = await this.axios.get(path, { params })

    return response.data
  }

  async post(path, payload) {
    const response = await this.axios.post(path, payload)

    return response.data
  }

  async put(path, payload) {
    const response = await this.axios.put(path, payload)

    return response.data
  }

    async patch(path, payload) {
    const response = await this.axios.patch(path, payload)

    return response.data
    }

  async delete(path, payload) {
    const response = await this.axios.delete(path,{data:payload})
    
    return response.data
  }
}
