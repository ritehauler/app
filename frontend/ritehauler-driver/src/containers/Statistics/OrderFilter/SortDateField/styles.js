import { StyleSheet } from "react-native";
import { Fonts, Colors, Metrics, ApplicationStyles } from "../../../../theme";

export default StyleSheet.create({
  containerStyle: {
    backgroundColor: Colors.background.primary,
    paddingHorizontal: Metrics.baseMargin,
    paddingVertical: Metrics.listSpace,
    borderColor: Colors.separator,
    borderRadius: Metrics.smallMargin / 2,
    borderWidth: Metrics.ratio(0.8)
  },
  dateInput: {
    alignItems: "flex-start",
    height: 16,
    borderWidth: 0
  },
  dateText: {
    fontFamily: Fonts.type.base,
    color: Colors.text.primary,
    fontSize: Fonts.size.xSmall
  },
  dateTouchBody: {
    height: 16
  },
  datePickerStyle: {
    marginLeft: Metrics.ratio(-36)
  }
});
