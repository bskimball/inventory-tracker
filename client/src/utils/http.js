import axios from "axios";

const http = axios.create({ baseURL: "http://localhost:8080" });

http.defaults.withCredentials = true;

export default http;
