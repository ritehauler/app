// @flow
import _ from "lodash";
import { connect } from "react-redux";
import React, { Component } from "react";
import { View } from "react-native";
import PropTypes from "prop-types";
import ScrollableTabView from "react-native-scrollable-tab-view";
import { SectionText, DriverPerformanceDetail, Tab } from "../../../components";
import styles from "./styles";
import { Metrics } from "../../../theme";
import Util from "../../../util";

// redux imports
import { constructDriverWeeklyStats } from "../../../reducers/reduxSelectors";

class WeeklyPerformanceView extends Component {
  static propTypes = {
    startDate: PropTypes.oneOfType([PropTypes.object, PropTypes.string]),
    currencyCode: PropTypes.string
  };

  static defaultProps = {
    startDate: {},
    currencyCode: ""
  };

  renderTabs(weeklyStats) {
    return (
      <ScrollableTabView
        style={styles.scrollTabStyle}
        prerenderingSiblingsNumber={1}
        isIOS
        page={0}
        renderTabBar={() => <Tab fontType="light" />}
      >
        {weeklyStats.map((item, index) =>
          this.renderDriverPerformanceView(item, index)
        )}
      </ScrollableTabView>
    );
  }

  renderDriverPerformanceView(item, index) {
    const { cellContainerStyle, cellStyle } = styles;

    return (
      <DriverPerformanceDetail
        key={item.day}
        tabLabel={item.day}
        data={item.data}
        customContainerStyle={cellContainerStyle}
        customCellStyle={cellStyle}
        currencyCode={this.props.currencyCode}
      />
    );
  }

  renderTitle() {
    return (
      <View style={{ paddingLeft: Metrics.baseMargin }}>
        <SectionText
          title="Daily Statistics"
          color="sectionLabel"
          type="base"
          size="normal"
        />
      </View>
    );
  }

  render() {
    if (!this.props.weeklyStats.length) return null;
    return (
      <View style={styles.containerStyle}>
        {this.renderTitle()}
        {this.renderTabs(this.props.weeklyStats)}
      </View>
    );
  }
}

const mapStateToProps = ({ weeklyStatistics }) => {
  return {
    weeklyStats: constructDriverWeeklyStats(weeklyStatistics.data.weekly)
  };
};

const actions = {};

export default connect(mapStateToProps, actions)(WeeklyPerformanceView);
