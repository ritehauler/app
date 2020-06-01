// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors } from "../../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    flexDirection: "row",
    paddingHorizontal: Metrics.baseMargin,
    paddingVertical: Metrics.baseMargin,
    backgroundColor: Colors.transparent,
    alignItems: "center"
  },
  title: {
    flex: 1
  },
  rightImage: {
    marginLeft: Metrics.listSpace
  }
});
