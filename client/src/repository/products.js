import dayjs from "dayjs";
import http from "../utils/http";
import sendToast from "../utils/sendToast";

export async function addProduct(input) {
  let additionMessage = input.serial_number
    ? `serial number ${input.serial_number} was added to inventory`
    : `part ${input.part_number} from lot ${input.lot_number} was added to inventory`;

  try {
    await http.post("/api/products", { ...input });
    sendToast({
      title: "Added to Inventory",
      body: additionMessage,
    });
  } catch (error) {
    sendToast({
      title: "Error",
      body: error?.message,
    });
  }
}

export async function releaseProduct(product) {
  let releaseMessage = product.serial_number
    ? `serial number ${product.serial_number} was released`
    : `part ${product.part_number} from lot ${product.lot_number} was released`;

  try {
    await http.put(`/api/products/${product.id}`, {
      date_released: dayjs().format("YYYY-MM-DD HH:mm:ss"),
    });
    sendToast({
      title: "Product Released",
      body: releaseMessage,
    });
  } catch (error) {
    sendToast({
      title: "Error",
      body: error?.message,
    });
  }
}
