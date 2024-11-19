import axios from "axios";

const axiosClient= axios.create({});

axiosClient.interceptors.request.use((config) => {

    return config
})
export default axiosClient;