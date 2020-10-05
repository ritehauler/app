// @flow
import { StyleSheet } from "react-native";
import { Colors } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: Colors.background.primary
  },
  message: {
    textAlign: "center",
    paddingHorizontal: 16,
    lineHeight: 26
  }
});
