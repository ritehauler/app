// @flow
import { connect } from "react-redux";
import React, { Component } from "react";
import { View } from "react-native";
import PropTypes from "prop-types";
import moment from "moment";
import {
  VictoryChart,
  VictoryLine,
  VictoryAxis,
  VictoryScatter,
  VictoryLabel,
  VictoryTheme,
  VictoryContainer
} from "victory-native";
import styles from "./styles";
import { Colors, Metrics, Fonts, ApplicationStyles } from "../../../theme";
import { PerformancePresenter } from "../../../presenter";
import { PERFORMANCE_TYPE, DATE_FORMAT } from "../../../constant";
import Util from "../../../util";

// redux imports
import { constructGraphData } from "../../../reducers/reduxSelectors";
import { reducer } from "redux-storage";

class GraphView extends Component {
  constructor(props) {
    super(props);
    this.state = {
      xValues: props.graphData.xValues,
      yValues: props.graphData.yValues,
      xAxisPoints: props.graphData.xAxisPoints,
      points: props.graphData.points,
      filterBy: props.graphData.filterBy
    };
  }

  renderGraph() {
    return (
      <VictoryChart
        theme={VictoryTheme.material}
        domainPadding={{ x: [0, 0], y: [20, 0] }}
        padding={{
          top: Metrics.ratio(25),
          bottom: Metrics.ratio(50),
          left: Metrics.ratio(50),
          right: Metrics.ratio(20)
        }}
        events={[
          {
            target: "data",
            eventHandlers: {
              onPress: () => alert("aa"),
              onClick: () => alert("bb")
              //onPressIn:
            }
          }
        ]}
      >
        {this.renderXAxis()}
        {this.renderYAxis()}
        {this.state.points.length > 1 && this.renderLine()}
        {this.renderPoints()}
      </VictoryChart>
    );
  }

  renderPoints() {
    return (
      <VictoryScatter
        style={{
          data: {
            fill: Colors.background.primary,
            stroke: Colors.background.backgroundSelect2,
            strokeWidth: Metrics.ratio(3)
          }
        }}
        size={Metrics.smallMargin / 1.5}
        data={this.state.points}
      />
    );
  }

  renderLine() {
    return (
      <VictoryLine
        style={{
          data: {
            stroke: Colors.background.backgroundSelect,
            strokeWidth: Metrics.ratio(4)
          }
        }}
        data={this.state.points}
      />
    );
  }

  renderYAxis() {
    return (
      <VictoryAxis
        style={{
          axis: {
            stroke: Colors.separator,
            strokeWidth: Metrics.dividerHeight
          },
          tickLabels: {
            fontSize: Fonts.size.xxxSmall,
            fontFamily: Fonts.type.base,
            fill: Colors.text.manatee,
            padding: Metrics.smallMargin
          },
          axis: { stroke: Colors.accent2 }
          // axisLabel: {
          //   fontFamily: Fonts.type.light,
          //   size: Fonts.size.xxSmall,
          //   stroke: Colors.text.primary
          // }
        }}
        // when not material
        //axisLabelComponent={<VictoryLabel dy={Metrics.ratio(45)} />}
        axisLabelComponent={<VictoryLabel dy={Metrics.ratio(30)} />}
        label="$ Earned"
        crossAxis
        dependentAxis
        tickValues={this.state.yValues}
      />
    );
  }

  renderXAxis() {
    return (
      <VictoryAxis
        style={{
          axis: {
            stroke: Colors.separator,
            strokeWidth: Metrics.dividerHeight
          },
          tickLabels: {
            fontSize: Fonts.size.xxxSmall,
            fontFamily: Fonts.type.base,
            fill: Colors.text.manatee,
            padding: Metrics.smallMargin
          },
          axis: { stroke: Colors.accent2 }
        }}
        // when not material theme
        //axisLabelComponent={<VictoryLabel dy={Metrics.ratio(1)} />}
        axisLabelComponent={<VictoryLabel dy={Metrics.ratio(20)} />}
        label={this.state.filterBy}
        tickValues={this.state.xAxisPoints}
        tickFormat={this.state.xValues}
        crossAxis
      />
    );
  }

  render() {
    return (
      <View style={styles.containerStyle} pointerEvents="none">
        {this.renderGraph()}
      </View>
    );
  }
}

const mapStateToProps = ({ statistics }) => {
  return {
    graphData: constructGraphData(statistics.data)
  };
};

const actions = {};

export default connect(
  mapStateToProps,
  actions
)(GraphView);
