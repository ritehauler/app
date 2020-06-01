import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";
import Utils from "../../util";

export default StyleSheet.create({
  containerStyle: {
    flex: 1,
    backgroundColor: Colors.background.login
  },
  list: {
    backgroundColor: Colors.background.login
  },
  listContainer: {
    paddingBottom: Metrics.listSpace * 7
  },
  listSeparatorStyle: {
    backgroundColor: Colors.background.primary,
    paddingHorizontal: Metrics.baseMargin,
    marginHorizontal: Metrics.baseMargin
  },
  spaceSeparatorStyle: {
    backgroundColor: Colors.separator,
    height: Metrics.dividerHeight
  },
  button: {
    // position: "absolute",
    // left: 0,
    // right: 0,
    // bottom: 0
  },
  feedbackContainerStyle: {
    backgroundColor: Colors.background.primary,
    paddingBottom: Metrics.baseMargin,
    paddingHorizontal: Metrics.baseMargin,
    marginHorizontal: Metrics.baseMargin
  },
  feedbackStyle: {
    borderWidth: Metrics.ratio(1),
    borderColor: Colors.border,
    marginTop: 0,
    marginBottom: 0,
    borderRadius: Metrics.ratio(2)
  },
  headerContainerStyle: {
    marginTop: Metrics.baseMargin
  },
  rateContainerStyle: {
    backgroundColor: Colors.background.primary,
    paddingHorizontal: Metrics.baseMargin,
    paddingVertical: Metrics.baseMargin * 1.5,
    marginHorizontal: Metrics.baseMargin,
    alignItems: "center"
  },
  labelStyle: {
    marginBottom: Utils.isPlatformAndroid()
      ? Metrics.ratio(10)
      : Metrics.ratio(20),
    fontSize: Utils.isPlatformAndroid() ? Metrics.ratio(15) : Metrics.ratio(10)
  }
});
