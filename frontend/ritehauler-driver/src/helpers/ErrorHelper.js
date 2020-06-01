import { Alert } from "react-native";
import utils from "../util";

class ErrorHelper {
  handleErrors(err, doAlert) {
    if (doAlert) {
      utils.showAlertWithDelay("Error!", err.message, 1000);
    }
  }
}

export default new ErrorHelper();
