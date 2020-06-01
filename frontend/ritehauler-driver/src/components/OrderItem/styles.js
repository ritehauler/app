import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";
import Utils from "../../util";

export default StyleSheet.create({
  containerStyle: {
    backgroundColor: Colors.background.primary,
    paddingHorizontal: Metrics.baseMargin,
    paddingVertical: Metrics.baseMargin,
    marginHorizontal: Metrics.baseMargin,
    flexDirection: "row"
  },
  circleImageStyle: {
    height: Metrics.thumbImageHeight,
    width: Metrics.thumbImageWidth,
    borderRadius: Metrics.thumbImageRadius
  },
  paymentWrapper: {
    flexDirection: "row",
    alignItems: "center",
    paddingTop: Metrics.ratio(2),
    flex: 1
  },
  checkImage: { width: Metrics.icon.medium, height: Metrics.icon.medium },
  checkRadius: {
    borderRadius: Metrics.icon.medium / 2
  },
  orderDetailWrapper: {
    marginLeft: Metrics.baseMargin,
    flex: 1,
    paddingTop: Utils.isPlatformAndroid()
      ? Metrics.widthPercentageToDP(0)
      : Utils.isPhoneX()
        ? Metrics.widthPercentageToDP(1.5)
        : Metrics.widthPercentageToDP(0.5)
  },
  personDetailWrapper: {
    flexDirection: "row",
    flex: 1,

    alignItems: "center"
  }
});
