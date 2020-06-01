// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    backgroundColor: Colors.background.secondary,
    padding: Metrics.baseMargin
  },
  cardImageAndUserName: {
    flexDirection: "row",
    marginBottom: Metrics.baseMargin,
    alignItems: "center"
  },
  userName: {
    marginLeft: Metrics.smallMargin
  },
  cardNumberContainer: {
    flexDirection: "row",
    marginTop: Metrics.smallMargin * 1.5,
    alignItems: "center",
    justifyContent: "center"
  },
  cardNumber: {
    flex: 1
  }
});
