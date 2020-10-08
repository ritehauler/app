import PropTypes from "prop-types";
import { Colors, Metrics, Fonts } from "../../theme";
import LinearGradient from "react-native-linear-gradient";

const React = require("react");
const createClass = require("create-react-class");
const {
  StyleSheet,
  Text,
  View,
  Animated,
  ViewPropTypes
} = require("react-native");
const Button = require("./Button");

const DefaultTabBar = createClass({
  propTypes: {
    goToPage: PropTypes.func,
    activeTab: PropTypes.number,
    tabs: PropTypes.array,
    backgroundColor: PropTypes.string,
    activeTextColor: PropTypes.string,
    inactiveTextColor: PropTypes.string,
    textStyle: Text.propTypes.style,
    tabStyle: ViewPropTypes.style,
    renderTab: PropTypes.func,
    underlineStyle: ViewPropTypes.style,
    fontType: PropTypes.string
  },

  getDefaultProps() {
    return {
      activeTextColor: Colors.text.primary,
      inactiveTextColor: Colors.text.placeholder,
      fontType: "bold",
      backgroundColor: null
    };
  },

  renderTabOption(name, page) {},

  renderTab(name, page, isTabActive, onPressHandler) {
    const { activeTextColor, inactiveTextColor, badge } = this.props;
    const textColor = isTabActive ? activeTextColor : inactiveTextColor;
    const fontFamily = Fonts.type[this.props.fontType];

    const showBagde = badge && badge.index === page && badge.count > 0;

    const badgeCount = badge ? badge.count : 0;

    const badgeLength = badge ? `${badgeCount}`.length : 0;

    const sizeCircle = 18 + (badgeLength - 1) * 4;

    return (
      <Button
        style={{ flex: 1 }}
        key={name}
        accessible
        accessibilityLabel={name}
        accessibilityTraits="button"
        onPress={() => onPressHandler(page)}
      >
        <View style={[styles.tab, this.props.tabStyle]}>
          <Text
            style={[
              styles.tabBarText,
              {
                color: textColor,
                fontFamily,
                fontWeight: isTabActive ? "bold" : "normal"
              }
            ]}
          >
            {name}
          </Text>
          {showBagde && (
            <View
              style={[
                styles.badgeContainer,
                {
                  width: Metrics.ratio(sizeCircle),
                  height: Metrics.ratio(sizeCircle),
                  borderRadius: Metrics.ratio(sizeCircle) / 2
                }
              ]}
            >
              <Text style={styles.badge}>{badgeCount}</Text>
            </View>
          )}
        </View>
      </Button>
    );
  },

  render() {
    const AnimatedLinearGradient = Animated.createAnimatedComponent(
      LinearGradient
    );
    const containerWidth = this.props.containerWidth;
    const numberOfTabs = this.props.tabs.length;
    const tabUnderlineStyle = {
      position: "absolute",
      width: containerWidth / numberOfTabs,
      backgroundColor: Colors.background.backgroundSelect,
      height:
        this.props.fontType === "light" ? Metrics.ratio(2) : Metrics.ratio(4),
      bottom: 0
    };

    const translateX = this.props.scrollValue.interpolate({
      inputRange: [0, 1],
      outputRange: [0, containerWidth / numberOfTabs]
    });
    return (
      <View
        style={[
          styles.tabs,
          { backgroundColor: this.props.backgroundColor },
          this.props.style
        ]}
      >
        {this.props.tabs.map((name, page) => {
          const isTabActive = this.props.activeTab === page;
          const renderTab = this.props.renderTab || this.renderTab;
          return renderTab(name, page, isTabActive, this.props.goToPage);
        })}
        <AnimatedLinearGradient
          style={[
            tabUnderlineStyle,
            {
              transform: [{ translateX }]
            },
            this.props.underlineStyle
          ]}
          colors={Colors.lgColArray}
          start={{ x: 0.0, y: 0 }}
          end={{ x: 1.5, y: 0 }}
        />
      </View>
    );
  }
});

const styles = StyleSheet.create({
  badgeContainer: {
    marginLeft: Metrics.ratio(2),
    backgroundColor: Colors.background.backgroundSelect2,
    top: Metrics.ratio(-6),
    alignItems: "center",
    justifyContent: "center"
  },
  badge: {
    backgroundColor: Colors.transparent,
    color: Colors.background.primary,
    textAlign: "center",
    fontSize: Fonts.size.xxxSmall,
    textAlignVertical: "center",
    fontFamily: Fonts.type.bold,
    paddingBottom: Metrics.ratio(1),
    paddingRight: Metrics.ratio(0.5)
  },
  tab: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    flexDirection: "row",
    paddingBottom: Metrics.ratio(10)
  },
  tabs: {
    height: Metrics.ratio(50),
    flexDirection: "row",
    justifyContent: "space-around",
    borderWidth: Metrics.ratio(1),
    borderTopWidth: 0,
    borderLeftWidth: 0,
    borderRightWidth: 0,
    borderColor: Colors.separator
  },
  tabBarText: {
    fontSize: Fonts.size.normal,
    fontFamily: Fonts.type.dBold,
    marginTop: Metrics.baseMargin * 0.6
  }
});

module.exports = DefaultTabBar;
