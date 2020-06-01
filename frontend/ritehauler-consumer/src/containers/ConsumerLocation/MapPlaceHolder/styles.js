import { StyleSheet } from "react-native";

import { Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    width: Metrics.screenWidth,
    position: "absolute",
    left: 0,
    top: 0,
    right: 0,
    bottom: 0,
    backgroundColor: "#f4f4f4"
  },
  image: {
    width: Metrics.screenWidth
  }
});
