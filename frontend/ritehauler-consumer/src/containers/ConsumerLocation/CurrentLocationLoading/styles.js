// @flow
import { StyleSheet } from "react-native";
import { Colors } from "../../../theme";

export default StyleSheet.create({
  container: {
    position: "absolute",
    left: 0,
    top: 0,
    right: 0,
    bottom: 0,
    backgroundColor: Colors.background.locationTranslucent,
    justifyContent: "center",
    alignItems: "center"
  }
});
