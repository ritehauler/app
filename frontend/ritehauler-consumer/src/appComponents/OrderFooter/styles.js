// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors } from "../../theme";

export default StyleSheet.create({
  container: {
    paddingBottom: Metrics.smallMargin
  },
  truckAndFeesInfo: {
    backgroundColor: Colors.background.secondary,
    marginTop: Metrics.baseMargin * 1.25,
    paddingHorizontal: Metrics.baseMargin,
    paddingBottom: Metrics.smallMargin * 0.75
  },
  separator: {
    height: 1,
    backgroundColor: Colors.background.separator
  },
  deliveryProfessionals: {
    backgroundColor: "white",
    padding: Metrics.baseMargin,
    marginTop: Metrics.baseMargin * 1.25,
    marginBottom: 0
  },
  truckContainer: {
    flexDirection: "row",
    marginTop: Metrics.baseMargin,
    marginBottom: Metrics.baseMargin * 0.75
  },
  truckTitle: {
    flex: 1
  },
  feesInfo: {
    marginTop: Metrics.smallMargin * 1.25,
    marginBottom: Metrics.smallMargin * 1.5
  },
  estimateCost: {
    marginVertical: Metrics.smallMargin * 1.25
  },
  confirmContainer: {
    flexDirection: "row",
    marginTop: Metrics.baseMargin
  },
  confirmText: {
    lineHeight: Metrics.baseMargin * 1.4,
    paddingRight: Metrics.baseMargin * 1.5
  }
});
