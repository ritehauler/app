// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors } from "../../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: Colors.background.secondary
  },
  title: {
    margin: Metrics.baseMargin
  },
  image: {
    width: Metrics.screenWidth / 3,
    height: Metrics.screenWidth / 3
  },
  description: {
    margin: Metrics.baseMargin
  }
});
