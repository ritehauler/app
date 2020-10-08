// @flow
import { connect } from "react-redux";
import React, { Component } from "react";
import { View } from "react-native";
import PropTypes from "prop-types";
import styles from "./styles";
import { SectionText, DriverPerformanceDetail } from "../../../components";
import Util from "../../../util";
import { PerformancePresenter } from "../../../presenter";
import { Metrics } from "../../../theme";

// redux imports
import { constructDriversMonthlyStats } from "../../../reducers/reduxSelectors";

const performanceStats = [
  { id: 0, title: "Orders", value: 22, last_value: 10, score: "18" },
  { id: 1, title: "Hours Online", value: 22, last_value: 170, score: "34" },
  { id: 2, title: "Earned %", value: 18, last_value: 20, score: "$ 200" },
  { id: 3, title: "Driver Cancel", value: 22, last_value: 3, score: "3" },
  { id: 4, title: "Rating", value: 2, last_value: 10, score: "4.3" },
  { id: 5, title: "Deliveries", value: 22, last_value: 10, score: "86%" }
];

class PerformanceView extends Component {
  static propTypes = {
    currencyCode: PropTypes.string
  };

  static defaultProps = {
    currencyCode: ""
  };

  renderDriverPerformanceView(performanceStats) {
    return (
      <DriverPerformanceDetail
        isWeekly
        data={performanceStats}
        currencyCode={"$"}
      />
    );
  }

  renderTitle() {
    return (
      <View style={{ marginLeft: Metrics.baseMargin }}>
        <SectionText
          title="Key Performance"
          color="sectionLabel"
          type="base"
          size="normal"
        />
      </View>
    );
  }

  render() {
    return (
      <View style={styles.containerStyle}>
        {this.renderTitle()}
        {this.renderDriverPerformanceView(this.props.monthlyStats)}
      </View>
    );
  }
}

const mapStateToProps = ({ statistics }) => {
  return {
    monthlyStats: constructDriversMonthlyStats(statistics.data.monthly)
  };
};

const actions = {};

export default connect(mapStateToProps, actions)(PerformanceView);
