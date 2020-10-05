import { StyleSheet, Platform } from "react-native";
import { Colors, Metrics } from "../../theme";
import Utils from "../../util";

export default StyleSheet.create({
  containerStyle: {
    flex: 1,
    backgroundColor: Colors.background.login
  },
  cardContainerStyle: {
    backgroundColor: Colors.background.primary,
    paddingHorizontal: Metrics.baseMargin,
    paddingVertical: Metrics.baseMargin
  },
  cardTextStyle: {
    marginBottom: Metrics.listSpace
  },
  cardOptionContainerStyle: {
    flexDirection: "row",
    justifyContent: "space-between",
    paddingHorizontal: Metrics.baseMargin
  },
  sortDateContainerStyle: {
    flexDirection: "row"
  },
  sortDateLeftStyle: {
    flex: 1,
    marginRight: Metrics.ratio(5)
  },
  sortDateRightStyle: {
    flex: 1,
    marginLeft: Metrics.ratio(5)
  },
  sliderTitleContainerStyle: {
    flexDirection: "row",
    alignItems: "center"
  },
  slideTitleTextStyle: {
    flex: 1
  },
  sliderRangeContainerStyle: {
    flexDirection: "row",
    marginTop: Metrics.ratio(-32)
  },
  cardSeparatorStyle: {
    backgroundColor: Colors.background.login,
    height: Metrics.baseMargin
  },
  trackFillStyle: {
    backgroundColor: Colors.background.backgroundSelect
  },
  trackEmptyStyle: {
    backgroundColor: Colors.separator
  },
  markerStyle: {
    ...Platform.select({
      ios: {
        height: Metrics.ratio(20),
        width: Metrics.ratio(20),
        borderRadius: Metrics.ratio(10),
        backgroundColor: Colors.accent,
        shadowColor: "#000000",
        shadowOffset: {
          width: 0,
          height: 3
        },
        shadowRadius: 1,
        shadowOpacity: 0.2
      },
      android: {
        height: Metrics.ratio(20),
        width: Metrics.ratio(20),
        borderRadius: Metrics.ratio(10),
        backgroundColor: Colors.accent
      }
    })
  },
  pressedMarkerStyle: {
    ...Platform.select({
      ios: {},
      android: {
        height: Metrics.ratio(20),
        width: Metrics.ratio(20),
        borderRadius: Metrics.ratio(10)
      }
    })
  },
  button: {
    position: "absolute",
    left: 0,
    right: 0,
    bottom: 0,
    paddingBottom: Utils.isPhoneX() ? Metrics.doubleBaseMargin : 0
  },
  navBar: {
    flexDirection: "row",
    height: Metrics.navBarHeight,
    width: Metrics.screenWidth,
    borderBottomWidth: Metrics.ratio(1),
    borderBottomColor: Colors.background.login,
    alignItems: "center",
    paddingTop: Utils.isPhoneX()
      ? Metrics.baseMargin + Metrics.smallMargin
      : Utils.isPlatformAndroid()
        ? 0
        : Metrics.baseMargin,
    backgroundColor: Colors.background.primary
  }
});
