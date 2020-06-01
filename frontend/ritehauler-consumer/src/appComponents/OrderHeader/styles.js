// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors } from "../../theme";

export default StyleSheet.create({
  date: {
    marginTop: Metrics.smallMargin * 0.5,
    marginBottom: Metrics.smallMargin * 1.5
  },
  items: {
    marginTop: Metrics.baseMargin,
    marginBottom: Metrics.smallMargin * 1.5
  },
  container: {
    backgroundColor: Colors.background.secondary
  },
  userContainer: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    marginRight: Metrics.baseMargin
  },
  orderStatus: {
    marginTop: Metrics.smallMargin
  },
  trackButton: {
    padding: Metrics.smallMargin / 2,
    borderColor: Colors.accent,
    borderWidth: 1,
    marginLeft: Metrics.baseMargin,
    marginTop: Metrics.smallMargin
  },
  cancelPaymentIcon: {
    marginTop: Metrics.smallMargin
  }
});
