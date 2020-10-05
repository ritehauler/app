// @flow
import React, { Component } from "react";
import StarRating from "react-native-star-rating";
import { Images, Metrics } from "../../theme";

export default class Rating extends Component {
  render() {
    const { ...rest } = this.props;
    return (
      <StarRating
        maxStars={5}
        starStyle={{ marginRight: Metrics.smallMargin }}
        emptyStar={Images.starEmpty}
        fullStar={Images.starFill}
        halfStar={Images.starFill}
        {...rest}
      />
    );
  }
}
